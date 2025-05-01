<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

/**
 * This message subscriber should apply to any message that deals
 * with the creation of an inspection plan.
 *
 * @ParMessageSubscriber(
 *   id = "new_inspection_plan",
 *   title = @Translation("New inspection plan"),
 *   status = TRUE,
 *   message = {
 *     "new_inspection_plan",
 *   },
 * )
 */
class NewInspectionPlanSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataInspectionPlan[] $inspection_plans */
      $inspection_plans = $this->getMessageHandler()->getPrimaryData($message);
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = [];

      foreach ($inspection_plans as $inspection_plan) {
        $partnerships = array_merge(
          $partnerships,
          $inspection_plan->getPartnerships()
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
      foreach ($people as $person) {
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
      /** @var ParDataInspectionPlan[] $inspection_plans */
      $inspection_plans = $this->getMessageHandler()->getPrimaryData($message);
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = [];

      foreach ($inspection_plans as $inspection_plan) {
        $partnerships = array_merge(
          $partnerships,
          $inspection_plan->getPartnerships()
        );
      }
    }
    catch (ParNotificationException) {
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
