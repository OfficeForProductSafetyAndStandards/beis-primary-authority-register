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
 *   id = "partnership_amendment_confirm",
 *   title = @Translation("View the partnership amendment confirmation journey"),
 *   status = TRUE,
 *   weight = 9,
 *   notification = {
 *     "new_partnership_amendment",
 *   },
 *   field = "field_partnership",
 * )
 */
class ParPartnershipAmendmentConfirm extends ParLinkActionBase implements ParTaskInterface {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'Confirm the partnership amendments';

  /**
   * {@inheritDoc}
   */
  #[\Override]
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
      $partnership_legal_entities = $partnership->getPartnershipLegalEntities();
      // Get only the partnership legal entities that are awaiting confirmation.
      $partnership_legal_entities = array_filter($partnership_legal_entities, fn($partnership_legal_entity) => $partnership_legal_entity->getRawStatus() === 'confirmed_authority');

      if (!empty($partnership_legal_entities)) {
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
        $destination = Url::fromRoute('par_partnership_amend_confirm_flows.review', ['par_data_partnership' => $par_data_partnership->id()]);

        return $destination instanceof Url &&
          $par_data_partnership->isActive() ?
            $destination :
            NULL;
      }
    }

    return NULL;
  }

}
