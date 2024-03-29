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
 * with a new partnership application, including new partnership amendments.
 *
 * @ParMessageSubscriber(
 *   id = "new_partnership",
 *   title = @Translation("New partnership"),
 *   status = TRUE,
 *   message = {
 *     "new_partnership_notification",
 *     "new_partnership_amendment",
 *   },
 * )
 */
class NewPartnershipSubscriber extends ParMessageSubscriberBase {

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
      $people = $partnership->getOrganisationPeople();
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
  public function getSubscribedEntities(MessageInterface $message, array $subscriptions = []): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($partnerships as $partnership) {
      // This message should be viewed by the organisation.
      $subscriptions = array_merge(
        $subscriptions,
        $partnership->getOrganisation() ?? [],
      );
    }

    return $subscriptions;
  }
}
