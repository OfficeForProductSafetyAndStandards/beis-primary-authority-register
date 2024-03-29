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
 * with the expiry or revocation of an inspection plan.
 *
 * @ParMessageSubscriber(
 *   id = "inspection_plan_expiry",
 *   title = @Translation("Inspection plan expiry"),
 *   status = TRUE,
 *   message = {
 *     "inspection_plan_expiry_warning",
 *     "revoke_inspection_plan",
 *   },
 * )
 */
class InspectionPlanExpirySubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
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
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    // This message should be viewed by the primary authority.
    foreach ($partnerships as $partnership) {
      $subscriptions = array_merge(
        $subscriptions,
        $partnership->getAuthority() ?? [],
      );
    }

    return $subscriptions;
  }
}
