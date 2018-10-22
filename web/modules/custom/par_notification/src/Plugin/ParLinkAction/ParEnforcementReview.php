<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Check if an enforcement is awaiting review.
 *
 * @ParLinkAction(
 *   id = "enforcement_review",
 *   title = @Translation("Enforcement notice review."),
 *   status = TRUE,
 *   weight = 1,
 *   notification = {
 *     "new_enforcement_notification",
 *   }
 * )
 */
class ParEnforcementReview extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    if ($message->hasField('field_enforcement_notice') && !$message->get('field_enforcement_notice')->isEmpty()) {
      $par_data_enforcement_notice = current($message->get('field_enforcement_notice')->referencedEntities());

      // The route for approving enforcement notices.
      $destination = Url::fromRoute('par_enforcement_review_flows.respond', ['par_data_enforcement_notice' => $par_data_enforcement_notice->id()]);

      if ($par_data_enforcement_notice->inProgress() && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
