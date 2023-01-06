<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that deals
 * with the creation of a new enquiry.
 *
 * @ParMessageSubscriber(
 *   id = "new_enquiry",
 *   title = @Translation("New enquiry"),
 *   status = TRUE,
 *   message = {
 *     "new_general_enquiry",
 *     "new_inspection_feedback",
 *   },
 * )
 */
class NewEnquirySubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataEnquiryInterface $enquiry_entities [] */
      $enquiry_entities = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($enquiry_entities as $enquiry_entity) {
      if ($enquiry_entity instanceof ParDataEnquiryInterface) {
        // This message should be viewed by the enforcing authority
        // and by the enforced organisation.
        try {
          $subscriptions = array_merge(
            $subscriptions,
            [$enquiry_entity->sender()],
            $enquiry_entity->receiver(),
          );
        }
        catch (ParDataException $e) {
          return [];
        }
      }
    }

    return $subscriptions;
  }
}
