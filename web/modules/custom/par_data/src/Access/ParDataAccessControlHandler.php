<?php

namespace Drupal\par_data\Access;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;

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
    // @see PAR-1462 - Do not allow access to any entities that are deleted.
    if ($entity->isDeleted()) {
      $result = AccessResult::forbidden()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    $account = $this->prepareUser($account);
    if ($account->hasPermission('bypass par_data access')) {
      $result = AccessResult::allowed()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }
    if (!$account->hasPermission('access ' . $this->entityTypeId . ' entities')) {
      $result = AccessResult::forbidden()->cachePerPermissions();
      return $return_as_object ? $result : $result->isAllowed();
    }

    $result = parent::access($entity, $operation, $account, TRUE)->cachePerPermissions();
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // @see PAR-1462 - Do not allow access to any entities that are deleted.
    if ($entity->isDeleted()) {
      return AccessResult::forbidden();
    }

    // Access to each par entity depends on whether it is an authority or organisation
    // related to that par person, or whether it is related to one of these authorities
    // or organisations.
    // All access checks are done using the relationship between a user account
    // and a par person entity, so we need all the user's par people.
    $par_data_manager = \Drupal::service('par_data.manager');
    $user_account = \Drupal\user\Entity\User::load($account->id());
    $is_member = $par_data_manager->isMember($entity, $user_account);

    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          $permission = 'view unpublished ' . $this->entityTypeId . ' entities';
        }
        else {
          $permission = 'access ' . $this->entityTypeId . ' entities';
        }

        // All users are allowed to view regardless of their membership.
        return AccessResult::allowedIfHasPermission($account, $permission);

        break;

      case 'update':
        $permission = 'edit ' . $this->entityTypeId . ' entities';

        // Only members can edit or update a par entity.
        if ($is_member === TRUE) {
          return AccessResult::allowedIfHasPermission($account, $permission);
        }
        break;

      case 'delete':
        $permission = 'delete ' . $this->entityTypeId . ' entities';

        // Only members can edit or update a par entity.
        if ($is_member === TRUE) {
          return AccessResult::allowedIfHasPermission($account, $permission);
        }
        break;

    }

    return AccessResult::forbidden();
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
