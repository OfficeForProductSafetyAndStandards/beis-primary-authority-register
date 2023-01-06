<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

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
  public function getSubscribedEntities(MessageInterface $message): array {
    try {
      /** @var ParDataEnforcementNotice[] $enforcement_notices */
      $enforcement_notices = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return [];
    }

    foreach ($enforcement_notices as $enforcement_notice) {
      // This message should be viewed by the enforcing authority
      // and the enforced organisation.
      $subscribed_entities = array_filter([
        ...$enforcement_notice->getEnforcingAuthority() ?? [],
        ...$enforcement_notice->getEnforcedOrganisation() ?? [],
      ]);
    }

    return $subscribed_entities ?? [];
  }
}
