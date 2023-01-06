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
 *   },
 *   field = "field_inspection_feedback",
 * )
 */
class ParInspectionFeedbackView extends ParLinkActionBase {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'View the inspection feedback';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_inspection_feedback = current($message->get($this->getPrimaryField())->referencedEntities());

      $destination = Url::fromRoute('par_inspection_feedback_view_flows.view_feedback', ['par_data_inspection_feedback' => $par_data_inspection_feedback->id()]);

      return $destination instanceof Url ?
        $destination :
        NULL;
    }

    return NULL;
  }
}
