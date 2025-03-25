<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

/**
 * This message subscriber should apply to any message that deals
 * with the approval or review of an enforcement notice.
 *
 * @ParMessageSubscriber(
 *   id = "reviewed_enforcement",
 *   title = @Translation("Reviewed enforcement notice"),
 *   status = TRUE,
 *   message = {
 *     "approved_enforcement",
 *     "reviewed_enforcement",
 *   },
 * )
 */
class ReviewedEnforcementSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataEnforcementNotice[] $enforcement_notice */
      $enforcement_notices = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException|ParDataException) {
      return $recipients;
    }

    foreach ($enforcement_notices as $enforcement_notice) {
      if ($enforcement_notice instanceof ParDataEnforcementNotice) {
        // This message should be sent to the enforcement officer who created the request.
        $creator = $enforcement_notice->getEnforcingPerson(TRUE);
        if ($creator) {
          $recipients[] = new ParRecipient(
            $creator->getEmail(),
            $creator->getFirstName(),
            $creator
          );
        }
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
      // and the enforced organisation.
      $subscriptions = array_merge(
        $subscriptions,
        $enforcement_notice->getEnforcingAuthority() ?? [],
        $enforcement_notice->getEnforcedOrganisation() ?? [],
      );
    }

    return $subscriptions ?? [];
  }
}
