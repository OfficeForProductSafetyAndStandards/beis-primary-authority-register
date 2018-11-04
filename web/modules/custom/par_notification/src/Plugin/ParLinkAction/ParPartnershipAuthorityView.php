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

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_partnership') && !$message->get('field_partnership')->isEmpty()) {
      $par_data_partnership = current($message->get('field_partnership')->referencedEntities());

      // The route for viewing enforcement notices.
      $destination = Url::fromRoute('par_partnership_flows.authority_details', ['par_data_partnership' => $par_data_partnership->id()]);

      if (!$par_data_partnership->inProgress() && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
