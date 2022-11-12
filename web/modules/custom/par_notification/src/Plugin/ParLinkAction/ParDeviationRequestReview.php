<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send the user to the deviation review page if it has not already been reviewed.
 *
 * @ParLinkAction(
 *   id = "deviation_review",
 *   title = @Translation("Review deviation request."),
 *   status = TRUE,
 *   weight = 1,
 *   notification = {
 *     "new_deviation_request",
 *     "new_deviation_response",
 *   }
 * )
 */
class ParDeviationRequestReview extends ParLinkActionBase implements ParTaskInterface {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_deviation_request';

  /**
   * {@inheritDoc}
   */
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField(self::PRIMARY_FIELD)
      || $message->get(self::PRIMARY_FIELD)->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var ParDataDeviationRequest[] $deviation_requests */
    $deviation_requests = $message->get(self::PRIMARY_FIELD)->referencedEntities();
    // If any of the deviation requests are awaiting approval this is not complete.
    foreach ($deviation_requests as $deviation_request) {
      if ($deviation_request->isAwaitingApproval()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  public function receive(MessageInterface $message) {
    if ($message->hasField(self::PRIMARY_FIELD)
      && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_deviation_request = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      $destination = Url::fromRoute('par_deviation_review_flows.respond', ['par_data_deviation_request' => $par_data_deviation_request->id()]);

      if ($par_data_deviation_request->isAwaitingApproval()
        && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
