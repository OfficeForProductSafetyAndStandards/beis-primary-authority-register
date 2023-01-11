<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\Entity\ParDataMembershipInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that deals
 * with the response to an enquiry.
 *
 * @ParMessageSubscriber(
 *   id = "enquiry_response",
 *   title = @Translation("Enquiry response"),
 *   status = TRUE,
 *   message = {
 *     "new_deviation_response",
 *     "new_enquiry_response",
 *     "new_inspection_feedback_response",
 *   },
 * )
 */
class EnquiryResponseSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function getSubscribedEntities(MessageInterface $message): array {
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataEnquiryInterface[] $enquiry_entities */
      $enquiry_entities = [];
      /** @var \Drupal\comment\CommentInterface $comments */
      $comments = $this->getMessageHandler()->getPrimaryData($message);
      foreach ($comments as $comment) {
        $enquiry_entities += $comment->getCommentedEntity();
      }
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($enquiry_entities as $enquiry_entity) {
      if ($enquiry_entity instanceof ParDataEnquiryInterface) {
        // This message should be viewed by the enforcing authority
        // and by the enforced organisation.
        try {
          $sender = [$enquiry_entity->sender()];
        }
        catch (ParDataException $e) {
          $sender = [];
        }
        try {
          $receiver = $enquiry_entity->receiver();
        }
        catch (ParDataException $e) {
          $receiver = [];
        }
        $subscriptions = array_merge(
          $subscriptions,
          $sender,
          $receiver,
        );
      }
    }

    return $subscriptions;
  }
}
