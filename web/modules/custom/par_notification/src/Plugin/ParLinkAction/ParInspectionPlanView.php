<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_notification\ParLinkActionBase;

/**
 * Send user to the enforcement page.
 *
 * @ParLinkAction(
 *   id = "inspection_plan_view",
 *   title = @Translation("View inspection plan."),
 *   status = TRUE,
 *   weight = 2,
 *   notification = {
 *     "new_inspection_plan",
 *     "inspection_plan_expiry_warning",
 *   },
 *   field = "field_inspection_plan",
 * )
 */
class ParInspectionPlanView extends ParLinkActionBase {

  /**
   * {@inheritdoc}
   */
  protected string $actionText = 'View the inspection plan';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField($this->getPrimaryField())
      && !$message->get($this->getPrimaryField())->isEmpty()) {
      $par_data_inspection_plan = current($message->get($this->getPrimaryField())
        ->referencedEntities());

      /** @var \Drupal\par_data\ParDataRelationship[] $partnership_relationships */
      $partnership_relationships = $par_data_inspection_plan ?
        $par_data_inspection_plan->getRelationships('par_data_partnership') :
        [];
      $par_data_partnership = !empty($partnership_relationships) ?
        current($partnership_relationships)->getEntity() :
        NULL;

      if ($par_data_partnership instanceof ParDataEntityInterface &&
          $par_data_inspection_plan instanceof ParDataEntityInterface) {
        // The route for viewing enforcement notices.
        $destination = Url::fromRoute(
          'par_partnership_flows.authority_inspection_plan_details',
          [
            'par_data_partnership' => $par_data_partnership->id(),
            'par_data_inspection_plan' => $par_data_inspection_plan->id(),
          ]
        );

        return $destination instanceof Url ?
          $destination :
          NULL;
      }
    }

    return NULL;
  }

}
