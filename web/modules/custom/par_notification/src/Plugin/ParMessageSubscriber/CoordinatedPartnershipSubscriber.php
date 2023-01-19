<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that should
 * be directed at the coordinator of a partnership.
 *
 * @ParMessageSubscriber(
 *   id = "coordinated_partnership",
 *   title = @Translation("Coordinated partnership"),
 *   status = TRUE,
 *   message = {
 *     "member_list_stale_warning",
 *   },
 * )
 */
class CoordinatedPartnershipSubscriber extends ParMessageSubscriberBase {

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
      // Send to the coordinator of this partnership.
      $emails = $partnership->getOrganisationPeople();
      array_walk($emails, function (&$value) {
        $value = $value->getEmail();
      });

      $recipients = array_merge(
        $recipients,
        $emails ?? [],
      );
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
      // This message should be viewed by the coordinator.
      $subscriptions = array_merge(
        $subscriptions,
        $partnership->getOrganisation() ?? [],
      );
    }

    return $subscriptions;
  }
}
