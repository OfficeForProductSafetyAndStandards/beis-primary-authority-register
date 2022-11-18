<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Entity\Query\QueryInterface;
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
 *   },
 *   field = "field_enforcement_notice",
 * )
 */
class ParEnforcementReview extends ParLinkActionBase implements ParTaskInterface {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'Approve the notification of enforcement action';

  /**
   * {@inheritDoc}
   */
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField($this->getPrimaryField())
      || $message->get($this->getPrimaryField())->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var ParDataEnforcementNotice[] $enforcement_notices */
    $enforcement_notices = $message->get($this->getPrimaryField())->referencedEntities();
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
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_enforcement_notice = current($message->get($this->getPrimaryField())->referencedEntities());

      // The route for approving enforcement notices.
      $destination = Url::fromRoute('par_enforcement_review_flows.respond', ['par_data_enforcement_notice' => $par_data_enforcement_notice->id()]);

      return $destination instanceof Url &&
        $par_data_enforcement_notice->inProgress() ?
          $destination :
          NULL;
    }

    return NULL;
  }
}
