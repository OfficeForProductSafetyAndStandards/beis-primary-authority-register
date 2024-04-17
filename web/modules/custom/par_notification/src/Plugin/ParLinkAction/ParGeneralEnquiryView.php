<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_notification\ParLinkActionBase;

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
 *   },
 *   field = "field_general_enquiry",
 * )
 */
class ParGeneralEnquiryView extends ParLinkActionBase {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'View the general enquiry';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_general_enquiry = current($message->get($this->getPrimaryField())->referencedEntities());

      if ($par_data_general_enquiry instanceof ParDataEntityInterface) {
        $destination = Url::fromRoute('par_enquiry_view_flows.view_feedback', ['par_data_general_enquiry' => $par_data_general_enquiry->id()]);

        return $destination instanceof Url ?
          $destination :
          NULL;
      }
    }

    return NULL;
  }

}
