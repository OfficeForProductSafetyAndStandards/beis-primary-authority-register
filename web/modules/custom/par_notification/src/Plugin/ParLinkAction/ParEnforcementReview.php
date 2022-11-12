<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Send user to the enforcement review page.
 *
 * @ParLinkAction(
 *   id = "enforcement_review",
 *   title = @Translation("Review enforcement notice."),
 *   status = TRUE,
 *   weight = 1,
 *   notification = {
 *     "new_enforcement_notification",
 *   }
 * )
 */
class ParEnforcementReview extends ParLinkActionBase implements ParTaskInterface {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_enforcement_notice';

  /**
   * {@inheritDoc}
   */
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField(self::PRIMARY_FIELD)
      || $message->get(self::PRIMARY_FIELD)->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var ParDataEnforcementNotice[] $enforcement_notices */
    $enforcement_notices = $message->get(self::PRIMARY_FIELD)->referencedEntities();
    // If any of the enforcement notices are awaiting approval this is not complete.
    foreach ($enforcement_notices as $enforcement_notice) {
      if ($enforcement_notice->inProgress()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function receive(MessageInterface $message) {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_enforcement_notice = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      // The route for approving enforcement notices.
      $destination = Url::fromRoute('par_enforcement_review_flows.respond', ['par_data_enforcement_notice' => $par_data_enforcement_notice->id()]);

      if ($par_data_enforcement_notice->inProgress() && $destination->access($this->user)) {
        return new RedirectResponse($destination->toString());
      }
    }
  }
}
