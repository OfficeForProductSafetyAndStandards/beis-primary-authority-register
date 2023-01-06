<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

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
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = [];
      /** @var ParDataInspectionPlan[] $inspection_plans */
      $inspection_plans = $this->getMessageHandler()->getPrimaryData($message);
      foreach ($inspection_plans as $inspection_plan) {
        $partnerships += $inspection_plan->getPartnerships();
      }
    }
    catch (ParNotificationException $e) {
      return $recipients;
    }

    // This message should be viewed by the primary authority
    // contacts on the partnership.
    foreach ($partnerships as $partnership) {
      $emails = array_column($partnership->getAuthorityPeople(), 'email');
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
    $subscriptions = $this->getSubscribedEntities($message);

    try {
      /** @var ParDataPartnership[] $partnerships */
      $partnerships = [];
      /** @var ParDataInspectionPlan[] $inspection_plans */
      $inspection_plans = $this->getMessageHandler()->getPrimaryData($message);
      foreach ($inspection_plans as $inspection_plan) {
        $partnerships += $inspection_plan->getPartnerships();
      }
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
