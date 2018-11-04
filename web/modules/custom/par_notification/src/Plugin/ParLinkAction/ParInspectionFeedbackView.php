<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send the user to view the inspection feedback.
 *
 * @ParLinkAction(
 *   id = "inspection_feedback_view",
 *   title = @Translation("View inspection feedback."),
 *   status = TRUE,
 *   weight = 1,
 *   notification = {
 *     "new_inspection_feedback",
 *     "new_inspection_feedback_response",
 *   }
 * )
 */
class ParInspectionFeedbackView extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_inspection_feedback') && !$message->get('field_inspection_feedback')->isEmpty()) {
      $par_data_inspection_feedback = current($message->get('field_inspection_feedback')->referencedEntities());

      $destination = Url::fromRoute('par_inspection_feedback_view_flows.view_feedback', ['par_data_inspection_feedback' => $par_data_inspection_feedback->id()]);

      if ($destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
