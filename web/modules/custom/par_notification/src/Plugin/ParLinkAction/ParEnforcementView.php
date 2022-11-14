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
 *   }
 * )
 */
class ParEnforcementView extends ParLinkActionBase {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_enforcement_notice';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_enforcement_notice = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

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
