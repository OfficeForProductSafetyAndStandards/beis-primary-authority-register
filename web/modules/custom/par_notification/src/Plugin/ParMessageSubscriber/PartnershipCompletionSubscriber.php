<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

/**
 * This message subscriber should apply to any message that deals
 * with the completion of a partnership.
 *
 * @ParMessageSubscriber(
 *   id = "partnership_completion",
 *   title = @Translation("Partnership completion"),
 *   status = TRUE,
 *   message = {
 *     "partnership_confirmed_notificati",
 *   },
 * )
 */
class PartnershipCompletionSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $recipients;
    }

    foreach ($partnerships as $partnership) {
      // This message should be sent to the primary authority contacts at the authority.
      /** @var ParDataPersonInterface $people */
      $people = $partnership->getAuthorityPeople();
      foreach ($people as $key => $person) {
        $recipients[] = new ParRecipient(
          $person->getEmail(),
          $person->getFirstName(),
          $person
        );
      }
    }

    return $recipients;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($partnerships as $partnership) {
      // This message should be viewed by the authority.
      $subscriptions = array_merge(
        $subscriptions,
        $partnership->getAuthority() ?? [],
      );
    }

    return $subscriptions;
  }
}
