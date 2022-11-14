<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;
use Drupal\par_data\Entity\ParDataPartnership;
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
class ParPartnershipComplete extends ParLinkActionBase implements ParTaskInterface {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_partnership';

  /**
   * {@inheritDoc}
   */
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField(self::PRIMARY_FIELD)
      || $message->get(self::PRIMARY_FIELD)->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var ParDataPartnership[] $partnerships */
    $partnerships = $message->get(self::PRIMARY_FIELD)->referencedEntities();
    // If any of the partnerships are awaiting business confirmation this is not complete.
    foreach ($partnerships as $partnership) {
      $incomplete_statuses = [
        $partnership->getTypeEntity()->getDefaultStatus(),
        'confirmed_authority'
      ];

      if (in_array($partnership->getRawStatus(), $incomplete_statuses)) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD) && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_partnership = current($message->get(self::PRIMARY_FIELD)->referencedEntities());

      // The route for viewing enforcement notices.
      $destination = Url::fromRoute('par_partnership_confirmation_flows.partnership_confirmation_authority_checklist', ['par_data_partnership' => $par_data_partnership->id()]);

      return $destination instanceof Url &&
        $par_data_partnership->inProgress() ?
          $destination :
          NULL;
    }

    return NULL;
  }
}
