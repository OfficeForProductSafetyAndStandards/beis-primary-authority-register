<?php

namespace Drupal\par_notification\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\message\MessageInterface;
use Drupal\message_expire\MessageExpiryManagerInterface;
use Drupal\par_notification\ParMessageHandlerInterface;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParSubscriptionManagerInterface;
use Drupal\search_api\ConsoleException;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for PAR Data.
 */
class ParNotificationCommands extends DrushCommands {

  /**
   * The notification subscription manager service.
   *
   * @var \Drupal\par_notification\ParSubscriptionManagerInterface
   */
  protected ParSubscriptionManagerInterface $subscriptionManager;

  /**
   * The message handler service.
   *
   * @var \Drupal\par_notification\ParMessageHandlerInterface
   */
  protected ParMessageHandlerInterface $messageHandler;

  /**
   * The message expiry manager service.
   *
   * @var \Drupal\message_expire\MessageExpiryManagerInterface
   */
  protected MessageExpiryManagerInterface $messageExpiryManager;

  /**
   * ParDataCommands constructor.
   *
   * @param ParSubscriptionManagerInterface $par_subscription_manager
   *   The notification subscription manager service.
   * @param ParMessageHandlerInterface $par_message_handler
   *   The message handler service.
   * @param MessageExpiryManagerInterface $message_expiry_manager
   *   The message expiry manager service.
   */
  public function __construct(ParSubscriptionManagerInterface $par_subscription_manager, ParMessageHandlerInterface $par_message_handler, MessageExpiryManagerInterface $message_expiry_manager) {
    parent::__construct();

    $this->subscriptionManager = $par_subscription_manager;
    $this->messageHandler = $par_message_handler;
    $this->messageExpiryManager = $message_expiry_manager;
  }

  public static function create(ContainerInterface $container, DrushContainer $drush): self {
      return new static(
        $container->get('plugin.manager.par_subscription_manager'),
        $container->get('par_notification.message_handler'),
        $container->get('message_expire.manager')
      );
  }

  /**
   * Warm the PAR Data caches.
   *
   * @validate-module-enabled par_notification
   *
   * @command par-notification:scrub-messages
   * @aliases pdm

   */
  public function scrub_messages() {
    $total_query = \Drupal::entityTypeManager()
      ->getStorage('message')
      ->getQuery('par_data_messages')
      ->accessCheck();
    $messages = array_unique($total_query->execute());

    // Set the count statistics.
    $count = [
      'total' => count($messages),
      'events' => [],
      'current' => 0,
      'updated' => 0,
      'deleted' => 0,
      'expired' => 0,
    ];

    // Can manually choose to warm other caches not listed as defaults.
    foreach ($messages as $mid) {
      $message = \Drupal::entityTypeManager()
        ->getStorage('message')
        ->load($mid);
      if (!$message instanceof MessageInterface) {
        continue;
      }

      // Increment the current index.
      $count['current']++;

      try {
        $primary_data = $this->messageHandler->getPrimaryData($message);
      }
      catch (ParNotificationException $e) {
        // Delete messages which are missing primary data.
        $message->delete();
        $count['deleted']++;
        continue;
      }

      // Identify whether any of the primary data has already been accounted for.
      $events = &$count['events'];
      $unique_data = array_filter($primary_data, function($data) use ($message, $events) {
        $event_key = implode(':', [
          $message->getTemplate()->id(),
          $data?->id() ?? $message->id(),
        ]);
        return !isset($count['events'][$event_key]);
      });

      // If there is no unique data this message can be discarded.
      if (empty($unique_data)) {
        $this->messageExpiryManager->expire([$message]);
        $count['expired']++;
        continue;
      }

      // Add the message to the event log.
      foreach ($unique_data as $data) {
        $event_key = implode(':', [
          $message->getTemplate()->id(),
          $data?->id() ?? $message->id(),
        ]);
        $events[$event_key] = $mid;
      }

      // Add any recipients to the 'field_to'.
      if ($emails = $this->subscriptionManager->getRecipientEmails($message)) {
        $message->set('field_to', $emails);
      }

      // Get the related entities.
      $related_entities = $this->subscriptionManager->getSubscribedEntities($message);
      $authorities = array_filter($related_entities, function ($related_entity) {
        return ('par_data_authority' === $related_entity->getEntityTypeId());
      });
      $organisations = array_filter($related_entities, function ($related_entity) {
        return ('par_data_organisation' === $related_entity->getEntityTypeId());
      });

      // Add the subscribed entities.
      if (!empty($authorities) && $message->hasField('field_target_authority')) {
        $message->set('field_target_authority', $authorities);
      }
      if (!empty($organisations) && $message->hasField('field_target_organisation')) {
        $message->set('field_target_organisation', $organisations);
      }

      if (!empty($authorities) || !empty($organisations)) {
        // Save the message.
        $message->save();
        $count['updated']++;
      }

      if ($count['current'] % 500 == 0) {
        $this->output->writeln(dt('@count messages out of @total processed with %updated messages updated, %removed removed and %expired expired', [
          '@count' => $count['current'],
          '@total' => $count['total'],
          '%updated' => $count['updated'],
          '%removed' => $count['deleted'],
          '%expired' => $count['expired'],
        ]));
      }
    }

    return "De-duplication of message entities is complete.";
  }

  /**
   * Check the health of search_api indexes.
   *
   * @param string|null $index
   *   The index id.
   * @param array $options
   *   (optional) An array of options.
   *
   * @throws \Drupal\search_api\ConsoleException
   *   If a batch process could not be created.
   *
   * @validate-module-enabled par_data
   * @validate-module-enabled search_api
   *
   * @command par-data:index-health
   * @option index-health
   *   Whether to check the index health.
   * @aliases pih

   */
  public function index_health($index = NULL, array $options = ['index-health' => NULL]) {
    $include_index_health = $options['index-health'];

    $index_storage = $this->entityTypeManager->getStorage('search_api_index');

    $indexes = $index_storage->loadMultiple([$index]);
    if (!$indexes) {
      return [];
    }

    foreach ($indexes as $index) {
      // Check the health of the server.
      $server_health = $index->isServerEnabled() ? $index->getServerInstance()->hasValidBackend() : FALSE;

      $indexed = $index->getTrackerInstance()->getIndexedItemsCount();
      $total = $index->getTrackerInstance()->getTotalItemsCount();

      $entity_types = $index->getEntityTypes();
      $count = 0;
      foreach ($entity_types as $key => $type) {
        $entity_storage = $this->entityTypeManager->getStorage($type);
        $count += $entity_storage->getQuery()->accessCheck()->count()->execute();
      }

      $index_health = (($total == $indexed) && ($indexed == $count));

      if (!$server_health) {
        throw new ConsoleException(dt('Server for index %index is not valid.', ['%index' => $index->id()]));
      }
      if ($include_index_health and !$index_health) {
        throw new ConsoleException(dt('Index %index has only indexed %indexed out of %total items (%count entities).', [
          '%index' => $index->id(),
          '%indexed' => $indexed,
          '%total' => $total,
          '%count' => $count,
        ]));
      }
    }

    return "Index health good.";
  }
}
