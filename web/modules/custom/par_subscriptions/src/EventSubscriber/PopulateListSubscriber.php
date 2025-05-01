<?php

namespace Drupal\par_subscriptions\EventSubscriber;

use Drupal\par_subscriptions\Entity\ParSubscriptionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_subscriptions\Entity\ParSubscriptionList;
use Drupal\par_subscriptions\ParSubscriptionManager;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageInterface;

class PopulateListSubscriber implements EventSubscriberInterface {

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * The source storage used to discover configuration changes.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $sourceStorage;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs the ConfigSnapshotSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   * @param \Drupal\Core\Config\StorageInterface $source_storage
   *   The source storage used to discover configuration changes.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ConfigManagerInterface $config_manager, StorageInterface $source_storage, EntityTypeManagerInterface $entity_type_manager) {
    $this->configManager = $config_manager;
    $this->sourceStorage = $source_storage;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Creates a config snapshot.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The Event to process.
   */
  public function onConfigImporterImport(ConfigImporterEvent $event) {
    // Get the new config being imported.
    $new_config = $event->getChangelist('create');

    // Get the config entity definition.
    $subscription_list_definition = $this->entityTypeManager
      ->getDefinition(ParSubscriptionManager::SUBSCRIPTION_TYPE_ENTITY);

    // Subscribe every user to the par_news list.
    $query = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->getQuery()
      ->accessCheck(FALSE);

    $subscription_manager = \Drupal::service('par_subscriptions.manager');
    $lists = $subscription_manager->getLists();

    foreach ($lists as $list) {
      // Skip lists that aren't being installed.
      $config_name = "{$subscription_list_definition->getConfigPrefix()}.$list";
      if (!in_array($config_name, $new_config)) {
        continue;
      }

      // Run the query after checking the config.
      $result = $query->execute();

      foreach ($result as $id) {
        $user = User::load($id);
        $subscription = $user->id() > 1 ?
          $subscription_manager->createSubscription($list, $user->getEmail()) :
          NULL;

        // Silently subscribe & verify the user.
        if ($subscription instanceof ParSubscriptionInterface) {
          $subscription->verify();
        }
      }
    }
  }

  /**
   * React to imported config.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    $events[ConfigEvents::IMPORT][] = ['onConfigImporterImport', 30];
    return $events;
  }
}
