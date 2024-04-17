<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\registered_organisations\OrganisationManagerInterface;

/**
 * Updates a legal entity name.
 *
 * @Action(
 *   id = "par_update_registered_legal_entity",
 *   label = @Translation("Update registered legal entity"),
 *   type = "system"
 * )
 */
class ParUpdateRegisteredLegalEntity extends ActionBase {

  /**
   * Get the registered_organisation manager.
   *
   * @return \Drupal\registered_organisations\OrganisationManagerInterface
   */
  public function getOrganisationManager(): OrganisationManagerInterface {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if (!$entity instanceof ParDataLegalEntity) {
      return;
    }

    if ($entity->isRegisteredOrganisation()) {
      $entity->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @todo Implement entity/action checks
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
