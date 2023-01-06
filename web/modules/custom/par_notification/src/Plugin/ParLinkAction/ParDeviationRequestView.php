<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send the user to view the deviation request if it has been reviewed.
 *
 * @ParLinkAction(
 *   id = "deviation_view",
 *   title = @Translation("View deviation request."),
 *   status = TRUE,
 *   weight = 2,
 *   notification = {
 *     "new_deviation_request",
 *     "reviewed_deviation_request",
 *     "new_deviation_response",
 *   },
 *   field = "field_deviation_request",
 * )
 */
class ParDeviationRequestView extends ParLinkActionBase {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'View the deviation request';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_deviation_request = current($message->get($this->getPrimaryField())->referencedEntities());

      $destination = Url::fromRoute('par_deviation_view_flows.view_deviation', ['par_data_deviation_request' => $par_data_deviation_request->id()]);

      return $destination instanceof Url &&
        !$par_data_deviation_request->isAwaitingApproval() ?
          $destination :
          NULL;
    }

    return NULL;
  }
}
