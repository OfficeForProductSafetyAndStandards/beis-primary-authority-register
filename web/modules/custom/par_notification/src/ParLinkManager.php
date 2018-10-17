<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a link management service for notifications.
 *
 * Automatically handling redirection to the primary action link
 * for any given notification message, including sequential
 * redirection where multiple pages need to be accessed one after
 * the other.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParLinkManager extends DefaultPluginManager {

  use LoggerChannelTrait;
  use StringTranslationTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The account object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * Constructs a ParLinkManager instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EntityTypeManagerInterface $entity_type_manager, AccountInterface $user) {
    parent::__construct(
      'Plugin/ParLinkAction',
      $namespaces,
      $module_handler,
      'Drupal\par_notification\ParLinkActionInterface',
      'Drupal\par_notification\Annotation\ParLinkAction'
    );

    $this->alterInfo('par_notification_link_action_info');
    $this->setCacheBackend($cache_backend, 'par_notification_link_action_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());

    $this->entityTypeManager = $entity_type_manager;
    $this->user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // Assign any default properties.
    if (!isset($definition['notification']) || empty($definition['notification']) || !is_array($definition['notification'])) {
      $definition['notification'] = NULL;
    }
    if (!isset($definition['weight']) || empty($definition['weight']) || !is_numeric($definition['weight'])) {
      $definition['weight'] = 0;
    }
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\par_notification\ParLinkActionInterface
   */
  public function createInstance($plugin_id, array $configuration = []) {
    $instance = parent::createInstance($plugin_id, $configuration);
    $instance->setUser($this->user);
    return $instance;
  }

  /**
   * Get only the enabled rules.
   */
  public function getDefinitions($only_active = FALSE) {
    $definitions = [];
    foreach (parent::getDefinitions() as $id => $definition) {
      if (!$only_active || !empty($definition['status'])) {
        $definitions[] = $definition;
      }
    }

    usort($definitions, function($a, $b) {
      return $a['weight'] > $b['weight'];
    });

    return $definitions;
  }

  /**
   * Get only the enabled rules that applies to this notification.
   */
  public function getDefinitionsByNotification($notification_type) {
    $definitions = [];
    if ($notification_type) {
      foreach ($this->getDefinitions(TRUE) as $id => $definition) {
        if (!$definition['notification'] || in_array($notification_type, $definition['notification'])) {
          $definitions[] = $definition;
        }
      }
    }

    return $definitions;
  }

  /**
   * Generate the link manager URL.
   *
   * @param integer|string $message_id
   *   Can be a message id or a message replacement token.
   *
   * @return \Drupal\Core\Url
   *   The generated URL
   */
  public function generateLink($message_id) {
    $link_options = ['absolute' => TRUE];
    return Url::fromRoute('par_notification.link_manager', ['message' => $message_id], $link_options);
  }

  /**
   * Redirect to the appropriate URL.
   *
   * Handles cases where the link is not accessible.
   *
   * @throws ParNotificationException
   *   When the link cannot be accessed after all the checks have been made.
   *
   * @return RedirectResponse
   */
  public function receive(RouteMatchInterface $route, MessageInterface $message) {
    // The current link manager link, used for sequential redirection.
    // This will be used until all prerequisite actions are completed.
    $link_manager_destination = Url::fromRouteMatch($route);
    $link_manager_destination_path = $link_manager_destination->getInternalPath();
    $link_manager_destination_query = ['query' => ['destination' => UrlHelper::encodePath($link_manager_destination_path)]];

    // Get all redirection plugins.
    $plugin_definitions = $this->getDefinitionsByNotification($message->getTemplate()->id());
    foreach ($plugin_definitions as $definition) {
      $plugin = $this->createInstance($definition['id'], []);

      // Set the return destination query, useful for sequential actions.
      $plugin->setReturnQuery($link_manager_destination_query);

      $response = $plugin->receive($message);
      // If the plugin returns a redirect response return this.
      if ($response instanceof RedirectResponse) {
        return $response;
      }
    }
  }

}
