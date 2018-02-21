<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Migrate coordinated partnership date field.
 *
 * @Action(
 *   id = "par_migrate_coordinated_partnership_date_field",
 *   label = @Translation("migrate partnership date field from a range to two fields"),
 *   type = "system"
 * )
 */
class ParMigrateCoordinatedPartnershipDateField extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      if (!empty($entity->get('membership_date')->value)) {
        $entity->get('date_membership_began')->setValue($entity->get('membership_date')->value);
      }

      // Set all coordinated partnerships to covered by inspection.
      $entity->get('covered_by_inspection')->setValue(TRUE);

      // Set all date_membership_ceased values to NULL.
      $entity->get('date_membership_ceased')->setValue(NULL);

      $entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @TODO Implement entity/action checks
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
