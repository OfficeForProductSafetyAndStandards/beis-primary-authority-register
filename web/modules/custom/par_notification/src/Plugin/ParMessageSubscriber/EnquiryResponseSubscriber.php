<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParRecipient;

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
  #[\Override]
  public function getRecipients(MessageInterface $message): array {
    $par_data_manager = \Drupal::service('par_data.manager');
    $recipients = parent::getRecipients($message);

    try {
      /** @var \Drupal\par_data\Entity\ParDataEnquiryInterface[] $enquiry_entities */
      $enquiry_entities = [];
      /** @var \Drupal\comment\CommentInterface[] $comments */
      $comments = $this->getMessageHandler()->getPrimaryData($message);
      foreach ($comments as $comment) {
        $enquiry_entities[] = $comment->getCommentedEntity();
      }
    }
    catch (ParNotificationException $e) {
      return $recipients;
    }

    foreach ($enquiry_entities as $enquiry_entity) {
      if ($enquiry_entity instanceof ParDataEnquiryInterface) {
        // This message should be sent to the enforcement officer who created the enquiry,
        // and to the people who have written responses to the enquiry.
        try {
          $creator = $enquiry_entity->creator();
          $recipients[] = new ParRecipient(
            $creator->getEmail(),
            $creator->getFirstName(),
            $creator
          );
        }
        catch (ParDataException $e) {

        }

        $replies = $enquiry_entity->getReplies();
        try {
          $authorities = array_merge([$enquiry_entity->sender()], $enquiry_entity->receiver());
          foreach ($replies as $reply) {
            // Identify if there is a ParDataPerson entity associated with the comment owner.
            foreach ($authorities as $authority) {
              $recipient = $par_data_manager->getUserPerson($reply->getOwner(), $authority);
              if ($recipient instanceof ParDataPersonInterface) {
                break;
              }
            }

            $recipients[] = new ParRecipient(
              $reply->getAuthorEmail(),
              $recipient instanceof ParDataPersonInterface ? $recipient->getName() : $reply->getAuthorName(),
              $recipient instanceof ParDataPersonInterface ? $recipient : $reply->getOwner(),
            );
          }
        }
        catch (ParDataException) {

        }
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
      /** @var \Drupal\par_data\Entity\ParDataEnquiryInterface[] $enquiry_entities */
      $enquiry_entities = [];
      /** @var \Drupal\comment\CommentInterface[] $comments */
      $comments = $this->getMessageHandler()->getPrimaryData($message);
      foreach ($comments as $comment) {
        $enquiry_entities[] = $comment->getCommentedEntity();
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
        catch (ParDataException) {
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
