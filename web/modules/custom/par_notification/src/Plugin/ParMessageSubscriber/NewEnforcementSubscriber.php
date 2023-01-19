<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that deals
 * with the raising of a new enforcement notice.
 *
 * @ParMessageSubscriber(
 *   id = "new_enforcement",
 *   title = @Translation("New enforcement"),
 *   status = TRUE,
 *   message = {
 *     "new_enforcement_notification",
 *   },
 * )
 */
class NewEnforcementSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataEnforcementNotice[] $enforcement_notices */
      $enforcement_notices = $this->getMessageHandler()->getPrimaryData($message);
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = [];

      foreach ($enforcement_notices as $enforcement_notice) {
        $partnerships = array_merge(
          $partnerships,
          $enforcement_notice->getPartnership()
        );
      }
    }
    catch (ParNotificationException $e) {
      return $recipients;
    }

    // Send this message to all primary authority contacts on the partnership.
    foreach ($partnerships as $partnership) {
      $emails = $partnership->getAuthorityPeople();
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
      /** @var ParDataEnforcementNotice[] $enforcement_notices */
      $enforcement_notices = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($enforcement_notices as $enforcement_notice) {
      // This message should be viewed by the enforcing authority
      // and the primary authority.
      $subscriptions = array_filter([
        $subscriptions,
        ...$enforcement_notice->getEnforcingAuthority() ?? [],
        ...$enforcement_notice->getPrimaryAuthority() ?? [],
      ]);
    }

    return $subscriptions;
  }
}
