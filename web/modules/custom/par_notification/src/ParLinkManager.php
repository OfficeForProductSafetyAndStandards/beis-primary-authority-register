<?php

namespace Drupal\par_notification;

use Drupal\par_notification\Annotation\ParLinkAction;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;
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
class ParLinkManager extends DefaultPluginManager implements ParLinkManagerInterface {

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
  protected $currentUser;

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
      ParLinkActionInterface::class,
      ParLinkAction::class
    );

    $this->alterInfo('par_notification_link_action_info');
    $this->setCacheBackend($cache_backend, 'par_notification_link_action_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());

    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $user;
  }

  /**
   * Get message template storage.
   *
   * @return EntityStorageInterface
   *   The entity storage for message template (bundle) entities.
   */
  public function getMessageTemplateStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('message_template');
  }

  /**
   * Get the current user.
   *
   * @return AccountInterface
   *   The current user.
   */
  public function getCurrentUser(): AccountInterface {
    return $this->currentUser;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
  public function createInstance($plugin_id, array $configuration = []): ?ParLinkActionInterface {
    /** @var ParLinkActionInterface $instance */
    $instance = parent::createInstance($plugin_id, $configuration);
    $instance->setUser($this->getCurrentUser());
    return $instance;
  }

  /**
   * Get only the enabled rules.
   *
   * @param bool $only_active
   *   Whether to return only active definitions.
   *
   * @return array
   *   An array of plugin definitions.
   */
  #[\Override]
  public function getDefinitions(bool $only_active = FALSE): array {
    $definitions = [];
    foreach (parent::getDefinitions() as $id => $definition) {
      if (!$only_active || !empty($definition['status'])) {
        $definitions[] = $definition;
      }
    }

    if (!empty($definitions))
      usort($definitions, fn($a, $b) => $a['weight'] <=> $b['weight']);{
    }

    return $definitions;
  }

  /**
   * Get only the enabled rules that applies to this notification.
   *
   * @param MessageTemplateInterface $notification_type
   *   The message template id (the message bundle).
   *
   * @return array
   *   An array of plugin definitions.
   */
  public function getDefinitionsByNotification(MessageTemplateInterface $notification_type): array {
    $definitions = [];
    foreach ($this->getDefinitions(TRUE) as $id => $definition) {
      if (!$definition['notification'] || in_array($notification_type->id(), $definition['notification'])) {
        $definitions[] = $definition;
      }
    }

    return $definitions;
  }

  /**
   * Get messages types that can contain tasks.
   *
   * @return MessageTemplateInterface[]
   *   An array of message templates that contain tasks.
   */
  public function getTaskTemplates(): array {
    $message_templates = $this->getMessageTemplateStorage()->loadMultiple();

    return array_filter($message_templates, fn($message_template) => !empty($this->retrieveTasks($message_template)));
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function retrieveTasks(MessageTemplateInterface $message_template): array {
    // Retrieve tasks once per notification type.
    $function_id = __FUNCTION__ . ':' . $message_template->id();
    $tasks = &drupal_static($function_id);
    if (isset($tasks)) {
      return $tasks;
    }

    $tasks = [];
    $plugin_definitions = $this->getDefinitionsByNotification($message_template);

    // Return only the link actions with tasks.
    foreach ($plugin_definitions as $definition) {
      $plugin = $this->createInstance($definition['id'], []);
      if ($plugin instanceof ParTaskInterface) {
        $tasks[$definition['id']] = $plugin;
      }
    }

    return $tasks;
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function generateLink(int|string $message_id): Url {
    $link_options = ['absolute' => TRUE];
    return Url::fromRoute('par_notification.link_manager', ['message' => $message_id], $link_options);
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function receive(RouteMatchInterface $route, MessageInterface $message): ?RedirectResponse {
    // The current link manager link, used for sequential redirection.
    // This will be used until all prerequisite actions are completed.
    $link_manager_destination = Url::fromRouteMatch($route);
    $link_manager_destination_path = $link_manager_destination->getInternalPath();
    $link_manager_destination_query = ['query' => ['destination' => UrlHelper::encodePath($link_manager_destination_path)]];

    // Get all redirection plugins.
    $plugin_definitions = $this->getDefinitionsByNotification($message->getTemplate());
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

    return NULL;
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function link(MessageInterface $message): ?Link {
    // Get all redirection plugins.
    $plugin_definitions = $this->getDefinitionsByNotification($message->getTemplate());
    foreach ($plugin_definitions as $definition) {
      $plugin = $this->createInstance($definition['id'], []);

      $action_link = $plugin->getLink($message);
      // If the plugin returns a redirect response return this.
      if ($action_link instanceof Link) {
        return $action_link;
      }
    }

    return NULL;
  }

  /**
   * Get the task status.
   *
   * @return bool|null
   *   Will return TRUE if ALL tasks are complete.
   *   Will return FALSE if at least ONE task is not complete
   *   Will return NULL if there are no tasks at all.
   */
  public function isComplete(MessageInterface $message): ?bool {
    // Get all redirection plugins.
    $tasks = $this->retrieveTasks($message->getTemplate());
    foreach ($tasks as $task) {
      if (!$task->isComplete($message)) {
        return FALSE;
      }
    }

    // If there are tasks, and they are all complete return TRUE.
    return !empty($tasks) ? TRUE : NULL;
  }

}
