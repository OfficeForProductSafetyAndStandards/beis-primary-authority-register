<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send user to the authority details stage of the partnership pages.
 *
 * @ParLinkAction(
 *   id = "partnership_authority_view",
 *   title = @Translation("View the authority details for partnership"),
 *   status = TRUE,
 *   weight = 10,
 *   notification = {
 *     "new_partnership_notification",
 *     "partnership_approved_notificatio",
 *     "partnership_confirmed_notificati",
 *   }
 * )
 */
class ParPartnershipAuthorityView extends ParLinkActionBase {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_partnership';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_partnership = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      // The route for viewing enforcement notices.
      $destination = Url::fromRoute('par_partnership_flows.authority_details', ['par_data_partnership' => $par_data_partnership->id()]);

      return $destination instanceof Url &&
        !$par_data_partnership->inProgress() ?
          $destination :
          NULL;
    }

    return NULL;
  }
}
