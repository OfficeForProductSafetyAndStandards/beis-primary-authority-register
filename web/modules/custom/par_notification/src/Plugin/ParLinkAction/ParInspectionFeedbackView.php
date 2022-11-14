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

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_inspection_feedback';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_inspection_feedback = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      $destination = Url::fromRoute('par_inspection_feedback_view_flows.view_feedback', ['par_data_inspection_feedback' => $par_data_inspection_feedback->id()]);

      return $destination instanceof Url ?
        $destination :
        NULL;
    }

    return NULL;
  }
}
