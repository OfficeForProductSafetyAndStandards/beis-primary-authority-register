<?php

namespace Drupal\par_data\Access;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;

/**
 * Access controller for trance entities.
 *
 * @see \Drupal\trance\Trance.
 */
class ParDataAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $account = $this->prepareUser($account);
    if ($account->hasPermission('bypass par_data access')) {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }
    if (!$account->hasPermission('access ' . $this->entityTypeId . ' entities')) {
      $result = AccessResult::forbidden()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    // All access checks are done using the relationship between a user account
    // and a par person entity.
    $par_data_manager = \Drupal::service('par_data.manager');
    $account_persons = $par_data_manager->getUserPeople($account);

    // Access to each par entity is depends on whether that par person is related
    // to any of the any authority or organisation, so check if this entity is one
    // of these entity types.
    if (in_array($entity->getEntityTypeId(), ['par_data_authority', 'par_data_organisation'])) {
      // Find out if any of this entity has any references to any of the
      // user's par person entities.
      $entities = [];
      foreach ($entity->getReferenceFieldsByTarget('par_data_person') as $field_name => $field) {
        $entities += $field->referencedEntities();
      }
    }
    // Otherwise check if this entity has any relationships to either of these
    // entity types.
    else {
      $parents = [];
      foreach ($entity->getReferenceFieldsByTarget('par_data_authority') as $field_name => $field) {
        $parents += $field->referencedEntities();
      }
      foreach ($entity->getReferenceFieldsByTarget('par_data_organisation') as $field_name => $field) {
        $parents += $field->referencedEntities();
      }
      
      $entities = [];
      foreach ($parents as $parent) {
        foreach ($entity->getReferenceFieldsByTarget('par_data_person') as $field_name => $field) {
          $entities += $field->referencedEntities();
        }
      }
    }

    $result = parent::access($entity, $operation, $account, TRUE)->cachePerPermissions();
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished ' . $this->entityTypeId . ' entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'access ' . $this->entityTypeId . ' entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit ' . $this->entityTypeId . ' entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete ' . $this->entityTypeId . ' entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add ' . $this->entityTypeId . ' entities');
  }

  /**
   * {@inheritdoc}
   */
  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = [], $return_as_object = FALSE) {
    $account = $this->prepareUser($account);

    if ($account->hasPermission('bypass ' . $this->entityTypeId . ' access')) {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }
    if (!$account->hasPermission('access ' . $this->entityTypeId . ' content')) {
      $result = AccessResult::forbidden()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    $result = parent::createAccess($entity_bundle, $account, $context, TRUE)->cachePerPermissions();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
