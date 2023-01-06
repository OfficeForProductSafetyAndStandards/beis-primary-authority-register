<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that deals
 * with the nomination of a partnership.
 *
 * @ParMessageSubscriber(
 *   id = "partnership_nominated",
 *   title = @Translation("Partnership nominated"),
 *   status = TRUE,
 *   message = {
 *     "partnership_approved_notificatio",
 *   },
 * )
 */
class PartnershipNominatedSubscriber extends ParMessageSubscriberBase {

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
      // This message should be viewed by the organisation.
      $authority_emails = array_column($partnership->getAuthorityPeople(), 'email');
      $organisation_emails = array_column($partnership->getOrganisationPeople(), 'email');
      $recipients = array_merge(
        $recipients,
        $authority_emails ?? [],
        $organisation_emails ?? [],
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
      // This message should be viewed by the primary authority
      // and the organisation.
      $subscriptions = array_merge(
        $subscriptions,
        $partnership->getAuthority() ?? [],
        $partnership->getOrganisation() ?? [],
      );
    }

    return $subscriptions;
  }
}
