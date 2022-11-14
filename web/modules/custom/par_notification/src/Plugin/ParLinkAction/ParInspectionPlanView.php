<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\ParDataRelationship;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
 *   }
 * )
 */
class ParInspectionPlanView extends ParLinkActionBase {

  /**
   * The field that holds the primary par_data entity that this message refers to.
   *
   * This changes depending on the message type / bundle.
   */
  const PRIMARY_FIELD = 'field_inspection_plan';

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    if ($message->hasField(self::PRIMARY_FIELD)
      && !$message->get(self::PRIMARY_FIELD)->isEmpty()) {
      $par_data_inspection_plan = current($message->get(self::PRIMARY_FIELD)
        ->referencedEntities());

      /** @var ParDataRelationship[] $partnership_relationships */
      $partnership_relationships = $par_data_inspection_plan ?
        $par_data_inspection_plan->getRelationships('par_data_partnership') :
        [];
      $par_data_partnership = !empty($partnership_relationships) ?
        current($partnership_relationships)->getEntity() :
        NULL;

      if ($par_data_partnership) {
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
