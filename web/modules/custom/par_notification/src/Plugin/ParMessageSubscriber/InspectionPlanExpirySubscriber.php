<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

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

    // This message should be viewed by the primary authority
    // contacts on the partnership.
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

    return $subscribed_entities;
  }
}
