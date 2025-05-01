<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

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
  #[\Override]
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
    catch (ParNotificationException) {
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
  #[\Override]
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataEnforcementNotice[] $enforcement_notices */
      $enforcement_notices = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException) {
      return $subscriptions;
    }

    foreach ($enforcement_notices as $enforcement_notice) {
      // This message should be viewed by the enforcing authority
      // and the primary authority.
      $subscriptions = array_merge(
        $subscriptions,
        $enforcement_notice->getEnforcingAuthority() ?? [],
        $enforcement_notice->getPrimaryAuthority() ?? [],
      );
    }

    return $subscriptions;
  }
}
