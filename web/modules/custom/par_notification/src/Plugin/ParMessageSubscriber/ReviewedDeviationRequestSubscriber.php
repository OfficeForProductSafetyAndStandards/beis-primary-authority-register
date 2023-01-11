<?php

namespace Drupal\par_notification\Plugin\ParMessageSubscriber;

use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataEnquiryInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_notification\ParMessageSubscriberBase;
use Drupal\par_notification\ParNotificationException;

/**
 * This message subscriber should apply to any message that deals
 * with the approval or review of a deviation request.
 *
 * @ParMessageSubscriber(
 *   id = "reviewed_deviation_request",
 *   title = @Translation("Reviewed deviation_request"),
 *   status = TRUE,
 *   message = {
 *     "reviewed_deviation_request",
 *   },
 * )
 */
class ReviewedDeviationRequestSubscriber extends ParMessageSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function getRecipients(MessageInterface $message): array {
    $recipients = parent::getRecipients($message);

    try {
      /** @var ParDataEnquiryInterface $deviation_requests [] */
      $deviation_requests = $this->getMessageHandler()->getPrimaryData($message);
      $partnerships = [];

      foreach ($deviation_requests as $deviation_request) {
        $partnerships = array_merge(
          $partnerships,
          $deviation_request->getPartnerships(),
        );
      }
    }
    catch (ParNotificationException|ParDataException $e) {
      return $recipients;
    }

    foreach ($partnerships as $partnership) {
      // This message should be viewed by the authority.
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
    $subscriptions = parent::getSubscribedEntities($message);

    try {
      /** @var ParDataEnquiryInterface $deviation_requests [] */
      $deviation_requests = $this->getMessageHandler()->getPrimaryData($message);
    }
    catch (ParNotificationException $e) {
      return $subscriptions;
    }

    foreach ($deviation_requests as $deviation_request) {
      if ($deviation_request instanceof ParDataEnquiryInterface) {
        // This message should be viewed by the enforcing authority.
        try {
          $sender = [$deviation_request->sender()];
        }
        catch (ParDataException $e) {
          $sender = [];
        }
        $subscriptions = array_merge(
          $subscriptions,
          $sender,
        );
      }
    }

    return $subscriptions ?? [];
  }
}
