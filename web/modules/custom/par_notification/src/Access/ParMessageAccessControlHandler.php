<?php

namespace Drupal\par_notification\Access;

use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\user\RoleInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\message\MessageInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\user\RoleStorageInterface;

/**
 * Access controller for message entities.
 *
 * @see \Drupal\message\Entity\Message.
 */
class ParMessageAccessControlHandler extends EntityAccessControlHandler implements EntityHandlerInterface {

  /**
   * The par data manager service.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The role storage service.
   *
   * @var RoleStorageInterface
   */
  protected $roleStorage;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('par_data.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * ParMessageAccessControlHandler constructor.
   *
   * @param EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param ParDataManagerInterface $par_data_manager
   *   The par data manager service.
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeInterface $entity_type, ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type);
    $this->parDataManager = $par_data_manager;
    $this->roleStorage = $entity_type_manager->getStorage('user_role');

  }

  /**
   * Get the ParDataHandler service.
   *
   * @return ParDataManagerInterface
   *   The par data manager service.
   */
  private function getParDataService() {
    return $this->parDataManager ?? \Drupal::service('par_data.manager');
  }

  /**
   * Get the ParDataHandler service.
   *
   * @return RoleStorageInterface
   *   The role storage service.
   */
  private function getRoleStorage() {
    return $this->roleStorage ?? \Drupal::entityTypeManager()->getStorage('user_role');
  }

  /**
   * Get the PAR Data associated with a message.
   *
   * @param \Drupal\message\MessageInterface $message
   *  The message to find entities for.
   *
   * @return \Drupal\par_data\Entity\ParDataEntityInterface[]
   *  An array of entities that related to this message.
   */
  private function getParData(MessageInterface $message): array {
    $par_data_fields = $this->getParDataService()->getReferences($message->getEntityTypeId(), $message->bundle());

    // Return if no PAR Data Reference fields found.
    if (!isset($par_data_fields[$message->getEntityTypeId()])) {
      return [];
    }

    // Loop through all the reference fields and return the entities.
    $message_fields = $par_data_fields[$message->getEntityTypeId()];
    $par_data_entities = [];
    foreach ($message_fields as $field_name => $field_definition) {
      if ($message->hasField($field_name)
          && $this->getParDataService()->getParEntityType($field_definition->getsetting('target_type'))) {
        $referenced_entities = $message->get($field_name)->referencedEntities();
        $par_data_entities = array_merge($par_data_entities, $referenced_entities);
      }
    }

    return $par_data_entities;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\message\MessageInterface $entity */
    if ($operation != 'view') {
      return parent::checkAccess($entity, $operation, $account);
    }

    // Check for role-based access.
    $permission = "receive {$entity->bundle()} notification";
    if (!$account->hasPermission($permission)) {
      return AccessResult::forbidden();
    }

    // Check whether any of the user's roles that apply to the notification
    // have the permission to bypass the relationship-based access.
    $bypass_relationship_access = FALSE;
    foreach ($this->getUserNotificationRoles($account, $entity) as $role) {
      if ($role->hasPermission('bypass par_data membership')) {
        $bypass_relationship_access = TRUE;
      }
    }

    // Check for relationship-based access.
    $related_entities = $this->getParData($entity);
    if (!empty($related_entities) && !$bypass_relationship_access) {
      $result = AccessResult::forbidden();
      foreach ($related_entities as $related_entity) {
        $user = User::load($account->id());
        if ($this->getParDataService()->isMember($related_entity, $user)) {
          $result = AccessResult::allowed();
          break;
        }
      }

      return $result;
    }

    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * Get all the user's roles that overlap with the roles
   * allowed to receive this message.
   *
   * This excludes any of the user's roles that are not set
   * to receive this message.
   *
   * @param AccountInterface $account
   *   The user account to cross-check with.
   * @param MessageInterface $message
   *   The message to get receiver roles for.
   *
   * @return RoleInterface[]
   *   An array of user roles.
   */
  public function getUserNotificationRoles(AccountInterface $account, MessageInterface $message): array {
    $permission = "receive {$message->bundle()} notification";

    // Get all the roles that have permission to receive this notification type.
    /** @var RoleInterface[] $notification_roles */
    $notification_roles = array_filter($this->getRoleStorage()->loadMultiple(), function ($role) use ($permission) {
      return ($role->hasPermission($permission));
    });

    // Get the intersection between the user's roles and the notifications roles.
    return array_udiff($notification_roles, $account->getRoles(), function ($a, $b) {
      return ($a->id() === $b->id());
    });
  }

}
