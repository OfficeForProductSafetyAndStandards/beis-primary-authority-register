<?php

namespace Drupal\par_actions\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\registered_organisations\DataException;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Drupal\registered_organisations\RegisterException;
use Drupal\registered_organisations\TemporaryException;

/**
 * Updates a legal entity name.
 *
 * @Action(
 *   id = "par_convert_legacy_legal_entity",
 *   label = @Translation("Convert legacy legal entity"),
 *   type = "system"
 * )
 */
class ParConvertLegacyLegalEntity extends ActionBase {

  /**
   * Get the registered_organisation manager.
   *
   * @return OrganisationManagerInterface
   */
  public function getOrganisationManager(): OrganisationManagerInterface {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function execute($entity = NULL) {
    if (!$entity instanceof ParDataLegalEntity) {
      return;
    }

    if ($entity->isLegacyEntity()) {
      try {
        // Update legacy legal entities.
        $updated = $entity->updateLegacyEntities();
        if ($updated) {
          $entity->save();
        }
      }
      catch (RegisterException|DataException) {
        // Catch unmanageable errors which should result in no further processing.
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // @TODO Implement entity/action checks
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
