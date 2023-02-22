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
      // This message should be sent to the authority and the organisation.
      /** @var ParDataPersonInterface $people */
      $people = array_merge(
        $partnership->getAuthorityPeople(),
        $partnership->getOrganisationPeople(),
      );
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
