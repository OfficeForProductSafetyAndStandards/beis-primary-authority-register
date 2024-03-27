<?php

namespace Drupal\par_notification\Drush\Commands;

use Drupal\par_notification\ParMessageHandlerInterface;
use Drupal\par_notification\ParSubscriptionManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\message_expire\MessageExpiryManagerInterface;

/**
 * A Drush commandfile.
 */
final class ParNotificationCommands extends DrushCommands {

  /**
   * Constructs a ParNotificationCommands object.
   */
  public function __construct(
    private readonly ParSubscriptionManagerInterface $subscriptionManager,
    private readonly ParMessageHandlerInterface $messageHandler,
    private readonly MessageExpiryManagerInterface $messageExpiryManager,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */ 
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.par_subscription_manager'),
      $container->get('par_notification.message_handler'),
      $container->get('message_expire.manager'),
    );
  }

  /**
   * Warm the PAR Data caches.
   */
  #[CLI\Command(name: 'par-notification:scrub-messages', aliases: ['psm'])]
  #[CLI\Usage(name: 'par-notification:scrub-messages', description: 'Usage description')]
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

      if ($count['current'] % 100 == 0) {
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

}
