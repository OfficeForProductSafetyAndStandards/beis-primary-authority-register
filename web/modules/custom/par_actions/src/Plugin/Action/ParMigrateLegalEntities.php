<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPartnership;

/**
 * Migrates legal entities.
 *
 * @Deprecated This plugin is no longer used.
 *
 * @Action(
 *   id = "par_migrate_legal_entities",
 *   label = @Translation("migrate legal entities"),
 *   type = "system"
 * )
 */
class ParMigrateLegalEntities extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataPartnership) {
      // Get partnership organisation entity.
      $par_data_organisation = $entity->getOrganisation(TRUE);

      // Get all legal entities on organisation.
      $legal_entities = $par_data_organisation ? $par_data_organisation->get('field_legal_entity')->getValue() : NULL;

      // Get all legal entity IDs.
      if ($entity->get('field_partnership_legal_entity')->isEmpty() && !empty($legal_entities)) {
        foreach ($legal_entities as $legal_entity) {
          $entity->addLegalEntity($legal_entity);
        }
        $entity->save();
      }
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
