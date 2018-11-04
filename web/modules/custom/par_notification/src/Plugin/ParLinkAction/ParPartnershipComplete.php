<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send user to the partnership completion pages.
 *
 * @ParLinkAction(
 *   id = "partnership_complete",
 *   title = @Translation("Vew the partnership completion journey"),
 *   status = TRUE,
 *   weight = 9,
 *   notification = {
 *     "new_partnership_notification",
 *   }
 * )
 */
class ParPartnershipOrganisationView extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_partnership') && !$message->get('field_partnership')->isEmpty()) {
      $par_data_partnership = current($message->get('field_partnership')->referencedEntities());

      // The route for viewing enforcement notices.
      $destination = Url::fromRoute('par_partnership_confirmation_flows.partnership_confirmation_authority_checklist', ['par_data_partnership' => $par_data_partnership->id()]);

      if ($par_data_partnership->inProgress() && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
