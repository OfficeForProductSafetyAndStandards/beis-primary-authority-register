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

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_general_enquiry';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_general_enquiry = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      $destination = Url::fromRoute('par_enquiry_view_flows.view_feedback', ['par_data_general_enquiry' => $par_data_general_enquiry->id()]);

      return $destination instanceof Url ?
        $destination :
        NULL;
    }

    return NULL;
  }
}
