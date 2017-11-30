<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Handles the sending of all notifications based on entity events.
*/
class ParNotifier extends DefaultPluginManager {

  use LoggerChannelTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * Notification default method.
   */
  const DEFAULT_METHOD = 'email';

  /**
   * The message manager service.
   */
  protected $messageManager;

  /**
   * Constructs a ParNotificationService object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\par_notification\ParMessageManager $message_manager
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, ParMessageManagerInterface $message_manager) {
    parent::__construct(
      'Plugin/ParNotifier',
      $namespaces,
      $module_handler,
      'Drupal\par_notification\ParNotifierInterface',
      'Drupal\par_notification\Annotation\ParNotifier'
    );

    $this->alterInfo('par_notifier_info');
    $this->setCacheBackend($cache_backend, 'par_notifier_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
    $this->messageManager = $message_manager;
  }

  /**
   * Deliver notification.
   *
   * @param UserInterface $recipient
   * @param string $message_id
   * @param string $plugin_id
   * @param EntityInterface $entity
   */
  public function notify($recipient, $message_id, $plugin_id = self::DEFAULT_METHOD, $entity = NULL) {
    $sender = User::load(\Drupal::currentUser()->id());
    $message = $this->messageManager->build($message_id, $recipient, $sender, $entity);

    if (!$message) {
      $replacements = [
        '%recipient' => $recipient,
        '%message' => $message_id,
      ];
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error("Failed to create the notification message for %recipient using the method %type", $replacements);
    }

    $plugin = $this->createInstance($plugin_id);
    try {
      if (!$delivered = $plugin->deliver($recipient, $message)) {
        $replacements = [
          '%type' => $plugin_id,
          '%message' => $message_id,
        ];
        $this->getLogger(self::PAR_LOGGER_CHANNEL)->error("Failed to deliver the notification %message using the method %type", $replacements);
      }
    }
    catch (ParNotificationException $e) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->error($e);
    }

  }
}
