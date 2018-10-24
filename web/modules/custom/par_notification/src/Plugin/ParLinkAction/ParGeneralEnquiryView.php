<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send the user to view the general enquiry.
 *
 * @ParLinkAction(
 *   id = "enquiry_view",
 *   title = @Translation("View general enquiry."),
 *   status = TRUE,
 *   weight = 1,
 *   notification = {
 *     "new_enquiry_response",
 *     "new_general_enquiry",
 *   }
 * )
 */
class ParGeneralEnquiryView extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_general_enquiry') && !$message->get('field_general_enquiry')->isEmpty()) {
      $par_data_general_enquiry = current($message->get('field_general_enquiry')->referencedEntities());

      $destination = Url::fromRoute('par_enquiry_view_flows.view_feedback', ['par_data_general_enquiry' => $par_data_general_enquiry->id()]);

      if ($destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
