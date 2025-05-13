<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;

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
 *   },
 *   field = "field_deviation_request",
 * )
 */
class ParDeviationRequestReview extends ParLinkActionBase implements ParTaskInterface {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'Approve the request to deviate from the inspection plan';

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField($this->getPrimaryField())
      || $message->get($this->getPrimaryField())->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var \Drupal\par_data\Entity\ParDataDeviationRequest[] $deviation_requests */
    $deviation_requests = $message->get($this->getPrimaryField())->referencedEntities();
    // If any of the deviation requests are awaiting approval this is not complete.
    foreach ($deviation_requests as $deviation_request) {
      if ($deviation_request->isAwaitingApproval()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField())
      && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_deviation_request = current($message->get($this->getPrimaryField())
        ->referencedEntities());

      if ($par_data_deviation_request instanceof ParDataEntityInterface) {
        $destination = Url::fromRoute('par_deviation_review_flows.respond', ['par_data_deviation_request' => $par_data_deviation_request->id()]);

        return $destination instanceof Url &&
          $par_data_deviation_request->isAwaitingApproval() ?
            $destination :
            NULL;
      }
    }

    return NULL;
  }

}
