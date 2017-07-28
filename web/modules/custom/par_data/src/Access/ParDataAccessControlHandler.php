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
        return AccessResult::allowedIfHasPermission($account, 'access ' . $this->entityTypeId . ' content');

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
