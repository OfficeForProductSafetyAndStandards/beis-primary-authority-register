<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;

/**
 * Send user to the partnership completion pages.
 *
 * @ParLinkAction(
 *   id = "partnership_complete",
 *   title = @Translation("View the partnership completion journey"),
 *   status = TRUE,
 *   weight = 9,
 *   notification = {
 *     "new_partnership_notification",
 *   },
 *   field = "field_partnership",
 * )
 */
class ParPartnershipComplete extends ParLinkActionBase implements ParTaskInterface {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'Complete the partnership application';

  /**
   * {@inheritDoc}
   */
  public function isComplete(MessageInterface $message): bool {
    // Check if this is a valid task.
    if (!$message->hasField($this->getPrimaryField())
      || $message->get($this->getPrimaryField())->isEmpty()) {
      throw new ParNotificationException('This message is invalid.');
    }

    /** @var \Drupal\par_data\Entity\ParDataPartnership[] $partnerships */
    $partnerships = $message->get($this->getPrimaryField())->referencedEntities();
    // If any of the partnerships are awaiting business confirmation this is not complete.
    foreach ($partnerships as $partnership) {
      $incomplete_statuses = [
        $partnership->getTypeEntity()->getDefaultStatus(),
        'confirmed_authority',
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
    if ($message->hasField($this->getPrimaryField()) && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_partnership = current($message->get($this->getPrimaryField())->referencedEntities());

      // The route for viewing enforcement notices.
      if ($par_data_partnership instanceof ParDataEntityInterface) {
        $destination = Url::fromRoute('par_partnership_confirmation_flows.partnership_confirmation_authority_checklist', ['par_data_partnership' => $par_data_partnership->id()]);

        return $destination instanceof Url &&
          $par_data_partnership->inProgress() ?
            $destination :
            NULL;
      }
    }

    return NULL;
  }

}
