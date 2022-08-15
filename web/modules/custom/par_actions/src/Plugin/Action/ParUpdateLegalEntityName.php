<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;

/**
 * Updates a legal entity name.
 *
 * @Action(
 *   id = "par_update_legal_entity_name",
 *   label = @Translation("Update legal entity name"),
 *   type = "system"
 * )
 */
class ParUpdateLegalEntityName extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof ParDataEntityInterface) {
      $organisation_register_manager = \Drupal::service('registered_organisations.organisation_manager');
      $companies_house_register = $organisation_register_manager->getDefinition('companies_house');

      // Only certain legal entity types are registered with Companies House.
      $companies_house_types = ['partnership', 'limited_company', 'public_limited_company', 'limited_partnership', 'limited_liability_partnership'];

      if ($entity->getType() && !empty($entity->getRegisteredNumber()) &&
        in_array($entity->get('legal_entity_type')->getString(), $companies_house_types)) {
        $company_profile = $organisation_register_manager->lookupOrganisation($entity->getRegisteredNumber(), $companies_house_register);

        $companies_house_name = $company_profile?->getName();
        if ($companies_house_name && $companies_house_name !== $entity->getName()) {
          // Update the name.
          $entity->get('registered_name')->setValue($companies_house_name);
          $entity->save();
        }
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
