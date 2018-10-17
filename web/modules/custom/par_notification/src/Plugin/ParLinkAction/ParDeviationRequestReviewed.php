<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Check if a deviation request has already been reviewed.
 *
 * @ParLinkAction(
 *   id = "deviation_reviewed",
 *   title = @Translation("Deviation already approved."),
 *   status = TRUE,
 *   weight = 2,
 *   notification = {
 *     "new_deviation_request",
 *     "reviewed_deviation_request",
 *   }
 * )
 */
class ParDeviationRequestReviewed extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_deviation_request') && !$message->get('field_deviation_request')->isEmpty()) {
      $par_data_deviation_request = current($message->get('field_deviation_request')->referencedEntities());

      $destination = Url::fromRoute('par_deviation_view_flows.view_deviation', ['par_data_deviation_request' => $par_data_deviation_request->id()]);

      if (!$par_data_deviation_request->isAwaitingApproval() && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
