<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_notification\ParLinkActionBase;
use Drupal\par_notification\ParNotificationException;
use Drupal\par_notification\ParTaskInterface;

/**
 * Send user to the partnership nomination pages.
 *
 * @ParLinkAction(
 *   id = "partnership_nominate",
 *   title = @Translation("Nominate the partnership journey"),
 *   status = TRUE,
 *   weight = 9,
 *   notification = {
 *     "partnership_nominate",
 *   },
 *   field = "field_partnership",
 * )
 */
class ParPartnershipNominate extends ParLinkActionBase implements ParTaskInterface {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'Nominate the partnership';

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
    // If any of the partnerships are awaiting nomination this is not complete.
    // Note: $partnership->inProgress() and $partnership->isActive() can't be used
    // because they check for other eventualities as well.
    foreach ($partnerships as $partnership) {
      $awaiting_statuses = [
        $partnership->getTypeEntity()->getDefaultStatus(),
        'confirmed_authority',
        'confirmed_business',
      ];

      if (in_array($partnership->getRawStatus(), $awaiting_statuses)) {
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

      // The route for nominating partnershipss.
      if ($par_data_partnership instanceof ParDataEntityInterface) {
        $destination = Url::fromRoute(
          'par_help_desks_flows.confirm_partnership',
          ['par_data_partnership' => $par_data_partnership->id()],
        );

        return $destination instanceof Url &&
          $par_data_partnership->inProgress() ?
            $destination :
            NULL;
      }
    }

    return NULL;
  }

}
