<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send user to the enforcement page.
 *
 * @ParLinkAction(
 *   id = "enforcement_view",
 *   title = @Translation("View enforcement notice."),
 *   status = TRUE,
 *   weight = 2,
 *   notification = {
 *     "approved_enforcement",
 *     "new_enforcement_notification",
 *     "reviewed_enforcement",
 *   },
 *   field = "field_enforcement_notice",
 * )
 */
class ParEnforcementView extends ParLinkActionBase {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'View the notification of enforcement action';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_enforcement_notice = current($message->get($this->getPrimaryField())->referencedEntities());

      // The route for viewing enforcement notices.
      $destination = Url::fromRoute('par_enforcement_send_flows.send_enforcement', ['par_data_enforcement_notice' => $par_data_enforcement_notice->id()]);

      return $destination instanceof Url &&
        !$par_data_enforcement_notice->inProgress() ?
          $destination :
          NULL;
    }

    return NULL;
  }
}
