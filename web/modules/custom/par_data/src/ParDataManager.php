<?php

namespace Drupal\par_data;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Sql\DefaultTableMapping;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataTypeInterface;
use Drupal\user\UserInterface;

/**
 * Manages all functionality universal to Par Data.
 */
class ParDataManager implements ParDataManagerInterface {

  use StringTranslationTrait;

  const PAR_AUTHORITY_ROLE_PA = 'primary_authority';
  const PAR_AUTHORITY_ROLE_EA = 'enforcing_authority';

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The drupal messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Iteration limit for recursive membership lookups.
   *
   * @var int
   */
  protected $membershipIterations = 5;

  /**
   * Debugging for the membership lookup.
   *
   * Change to TRUE to get an onscreen output.
   *
   * @var bool
   */
  protected $debug = FALSE;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity bundle info service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    MessengerInterface $messenger,
    RendererInterface $renderer,
    /**
     * The current user.
     */
    protected AccountProxyInterface $currentUser,
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->messenger = $messenger;
    $this->renderer = $renderer;
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   *   The injected messenger service.
   */
  public function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  /**
   * Get renderer service.
   *
   * @return \Drupal\Core\Render\RendererInterface
   *   The injected renderer service.
   */
  public function getRenderer(): RendererInterface {
    return $this->renderer;
  }

  /**
   * Get current user.
   *
   * @return mixed
   *   The injected current user.
   */
  public function getCurrentUser() {
    return $this->currentUser;
  }

  /**
   * Get the entity field manager.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   The injected Entity Field Manager service.
   */
  public function getEntityFieldManager() {
    return $this->entityFieldManager;
  }

  /**
   * Getter the Par Data Manager with a reduced iterator.
   */
  public function getReducedIterator($iterations = 0) {
    $new_par_data_manager = clone $this;
    $new_par_data_manager->membershipIterations = $iterations;
    return $new_par_data_manager;
  }

  /**
  * {@inheritdoc}
  */
  #[\Override]
  public function getParEntityTypes(): array {
    // We're obviously assuming that all par entities begin with this prefix.
    $par_entity_prefix = 'par_data_';
    $par_entity_types = [];
    $entity_type_definitions = $this->entityTypeManager->getDefinitions();
    foreach ($entity_type_definitions as $definition) {
      $bundle = $definition->getBundleEntityType();
      if ($definition instanceof ContentEntityType
        && isset($bundle)
        && str_starts_with($bundle, $par_entity_prefix)
      ) {
        $par_entity_types[$definition->id()] = $definition;
      }
    }
    return $par_entity_types ?: [];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getParEntityType(string $type): ?EntityTypeInterface {
    $types = $this->getParEntityTypes();
    return $types[$type] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEntityBundleDefinition(EntityTypeInterface $definition): ?EntityTypeInterface {
    return $definition->getBundleEntityType() ? $this->entityTypeManager->getDefinition($definition->getBundleEntityType()) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getParBundleEntity(string $type, $bundle = NULL): ?ParDataTypeInterface {
    $entity_type = $this->getParEntityType($type);
    $definition = $this->entityTypeManager->hasDefinition($type) ? $this->getEntityBundleDefinition($entity_type) : NULL;
    $bundles = $definition ? $this->getEntityTypeStorage($definition->id())->loadMultiple() : NULL;
    $bundle = $bundles && !empty($bundles[$bundle]) ? $bundles[$bundle] : current($bundles);
    return $bundle ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getEntityTypeStorage($definition): ?EntityStorageInterface {
    return $this->entityTypeManager->getStorage($definition) ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewBuilder($entity_type) {
    return $this->entityTypeManager->getViewBuilder($entity_type);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getFieldDefinition(string $type, string $field, $bundle = NULL): ?FieldDefinitionInterface {
    if (!$bundle) {
      $bundle_definition = $this->getParBundleEntity($type, $bundle);
      $bundle = $bundle_definition?->id();
    }

    $entity_fields = $this->entityFieldManager->getFieldDefinitions($type, $bundle);
    return $entity_fields[$field] ?? NULL;
  }

  /**
   * Get the settings for a given entity, field and view mode.
   */
  public function getFieldDisplay($entity, $field, $view_mode = 'default') {
    $view_display = $this->entityTypeManager
      ->getStorage('entity_view_display')
      ->load($entity->getEntityTypeId() . '.' . $entity->bundle() . '.' . $view_mode);

    return isset($view_display) && $view_display->getComponent($field->getName()) ? $view_display->getComponent($field->getName()) : ['label' => 'hidden'];
  }

  /**
   * Get the default for a field.
   */
  public function getFieldDefaults($entity_type, $bundle, $field) {
    $field_definition = $this->getFieldDefinition($entity_type, $field, $bundle);
    return $field_definition ? $field_definition->getDefaultValueLiteral() : [];
  }

  /**
   * Get all references for a given entity.
   *
   * @param string $type
   *   The entity type to search for.
   * @param string $bundle
   *   The entity bundle to search for.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of field definitions keyed by the entity type they are attached
   *   to.
   */
  public function getReferences($type, $bundle) {
    $reference_fields = [];

    // First get all the PAR Data entities referenced by this entity type.
    foreach ($this->entityFieldManager->getFieldDefinitions($type, $bundle) as $field_name => $definition) {
      if ($definition->getType() === 'entity_reference' && $this->getParEntityType($definition->getsetting('target_type'))) {
        $reference_fields[$type][$field_name] = $definition;
      }
    }

    // Second get all the entities that reference this entity type.
    $entity_references = $this->entityFieldManager->getFieldMapByFieldType('entity_reference');
    foreach ($entity_references as $referring_entity_type => $fields) {
      // Ignore all fields on the current entity type and all non PAR entities.
      if ($type === $referring_entity_type || !$this->getParEntityType($referring_entity_type)) {
        continue;
      }

      $field_definitions = [];
      foreach ($this->entityTypeBundleInfo->getBundleInfo($referring_entity_type) as $bundle => $bundle_definition) {
        $field_definitions += $this->entityFieldManager->getFieldDefinitions($referring_entity_type, $bundle);
      }

      foreach ($fields as $field_name => $field) {
        // Get field definition if available.
        if (!isset($field_definitions[$field_name])) {
          continue;
        }

        $field_definition = $field_definitions[$field_name];
        if ($field_definition->getsetting('target_type') === $type) {
          $reference_fields[$referring_entity_type][$field_name] = $field_definition;
        }
      }
    }

    return $reference_fields;
  }

  /**
   * Get the entities related to each other.
   *
   * Follows some rules to make sure it doesn't go round in loops.
   *
   * @param mixed $entity
   *   The entity being looked up.
   * @param array $entities
   *   The entities relationship tree.
   * @param int $iteration
   *   The depth of relationships to go before stopping.
   * @param bool $action
   *   Force the lookup of relationships that would otherwise be ignored.
   * @param string $debug_tree
   *   Debugging String.
   *
   * @return \Drupal\Core\Entity\EntityInterface[][]
   *   An array of entities keyed by entity type.
   */
  public function getRelatedEntities($entity, $entities = [], $iteration = 0, $action = NULL, &$debug_tree = '') {
    if (!$entity instanceof ParDataEntityInterface) {
      return $entities;
    }

    // Allow a debug tree to be built.
    if ($this->debug) {
      $debug_tree .= str_repeat('&mdash;', $iteration) . $entity->uuid() . ':' . $entity->getEntityTypeId() . ':' . $entity->label() . PHP_EOL;
    }

    // Set the entity.
    if (!isset($entities[$entity->uuid()])) {
      $entities[$entity->uuid()] = $entity;
    }

    // Make sure the entity isn't too distantly related
    // to limit recursive relationships.
    if ($iteration >= $this->membershipIterations) {
      return $entities;
    }
    else {
      $iteration++;
    }

    // Get all the relationships based on the given action.
    $relationships = $entity->getRelationships(NULL, $action);

    // Remove any universally banned relationships.
    $relationships = array_filter($relationships, function ($relationship) use ($iteration) {
      // Do not follow relationships from secondary people.
      if ($iteration > 1 && $relationship->getBaseEntity()->getEntityTypeId() === 'par_data_person') {
        return FALSE;
      }

      return TRUE;
    });

    // Loop through all relationships.
    foreach ($relationships as $uuid => $relationship) {
      // Lookup any further relationships.
      $entities = $this->getRelatedEntities($relationship->getEntity(), $entities, $iteration, $action, $debug_tree);
    }

    // Output debugging.
    if ($iteration === 1 && $this->debug) {
      $tree = Markup::create(nl2br("<br>" . $debug_tree));
      $this->getMessenger()->addMessage(t('New relationship tree: @tree', ['@tree' => $tree]));
    }

    return $entities;
  }

  /**
   * Determine whether a user account is a member of any given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity to check membership on.
   * @param \Drupal\user\UserInterface $account
   *   A user account to check for.
   *
   * @return bool
   *   Returns whether the account is a part of a given entity.
   */
  public function isMember(EntityInterface $entity, UserInterface $account) {
    return isset($this->hasMembershipsByType($account, $entity->getEntityTypeId())[$entity->uuid()]);
  }

  /**
   * Determine which entities a user is a part of.
   *
   * @param \Drupal\user\UserInterface $account
   *   A user account to check for.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns an array of entities keyed by entity type and then by entity id.
   *
   * @todo Fix problem inefficient coding here.
   *  Sorry, couldn't fix this before the support contract was pulled
   *  but this method is inefficient and needs re-writing. There are much
   *  better ways of calculating which entity actions can be performed.
   * @see https://regulatorydelivery.atlassian.net/wiki/spaces/PA/pages/40894490/Par+Access+Actions
   */
  public function hasMemberships(UserInterface $account, $direct = FALSE) {
    // This method will run about a thousand times if not given the bird.
    $function_id = __FUNCTION__ . $account->get('mail')->getString() . (($direct) ? 'true' : 'false');
    $memberships = &drupal_static($function_id);
    if (!empty($memberships)) {
      return $memberships;
    }

    $account_people = $this->getUserPeople($account);

    // When we say direct we really mean by a maximum factor of two.
    // Because we must first jump through one of the core membership
    // entities, i.e. authorities or organisations.
    $object = $direct ? $this->getReducedIterator(2) : $this;

    $memberships = [];
    foreach ($account_people as $person) {
      $memberships = $object->getRelatedEntities($person, $memberships, 0, 'manage');
    }

    // Do not return any deleted entities.
    // @see PAR-1462 - Removing all deleted entities from loading.
    $memberships = array_filter($memberships, fn($membership) => !$membership instanceof ParDataEntityInterface || !$membership->isDeleted());

    return !empty($memberships) ? $memberships : [];
  }

  /**
   * Determine which entities of a given type the user is part of.
   *
   * @param \Drupal\user\UserInterface $account
   *   A user account to check for.
   * @param string $type
   *   An entity type to filter on the return on.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns the entities for the given type.
   */
  public function hasMembershipsByType(UserInterface $account, $type, $direct = FALSE) {
    $memberships = $this->hasMemberships($account, $direct);

    $memberships = array_filter($memberships, fn($membership) => $type === $membership->getEntityTypeId());

    return $memberships;
  }

  /**
   * Determine whether there are any in progress memberships of a given type.
   *
   * @param \Drupal\user\UserInterface $account
   *   A user account to check for.
   * @param string $type
   *   An entity type to filter on the return on.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Returns the entities for the given type.
   */
  public function hasInProgressMembershipsByType(UserInterface $account, $type, $direct = FALSE) {
    $memberships = $this->hasMembershipsByType($account, $type, $direct);

    $memberships = array_filter($memberships, fn($membership) => $membership->inProgress());

    return $memberships;
  }

  /**
   * Determine whether there are any in memberships of a given type that have been commented on.
   *
   * @param \Drupal\user\UserInterface $account
   *   A user account to check for.
   * @param string $type
   *   An entity type to filter on the return on.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|bool
   *   Returns the entities for the given type.
   */
  public function hasNotCommentedOnMembershipsByType(UserInterface $account, $type, $direct = FALSE) {
    $memberships = $this->hasMembershipsByType($account, $type, $direct);

    $memberships = array_filter($memberships, function ($membership) use ($account) {
      $primary_authority = $membership->getPrimaryAuthority(TRUE);
      $enforcing_authority = $membership->getEnforcingAuthority(TRUE);

      if ($primary_authority_user = $this->getUserPerson($account, $primary_authority)) {
        $authority_role = self::PAR_AUTHORITY_ROLE_EA;
      }
      elseif ($this->getUserPerson($account, $enforcing_authority)) {
        $authority_role = self::PAR_AUTHORITY_ROLE_EA;
      }

      $messages = $membership->getReplies();
      if (isset($authority_role) && !empty($messages)) {
        foreach ($messages as $message) {
          if ($this->getUserPerson($message->getOwner(), $primary_authority) && $authority_role === self::PAR_AUTHORITY_ROLE_EA) {
            return FALSE;
          }
          elseif ($this->getUserPerson($message->getOwner(), $enforcing_authority) && $authority_role === self::PAR_AUTHORITY_ROLE_EA) {
            return FALSE;
          }
        }
      }
      else {
        return empty($messages);
      }
      return TRUE;
    });

    return $memberships;
  }

  /**
   * Checks if the person is a member of any authority.
   */
  public function isMemberOfAuthority($account) {
    return $account ? $this->hasMembershipsByType($account, 'par_data_authority', TRUE) : NULL;
  }

  /**
   * Checks if the person is a member of any organisation.
   */
  public function isMemberOfOrganisation($account) {
    return $account ? $this->hasMembershipsByType($account, 'par_data_organisation', TRUE) : NULL;
  }

  /**
   * Helper function to get all the roles filled within a users's authorities.
   *
   * @param \Drupal\par_data\Entity\ParDataEntityInterface[] $entities
   *   The authority entities to look for roles in.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   If a user is passed their roles will be ignored.
   *
   * @return array
   *   An array of member's keyed by authority and then role id.
   */
  public function getRolesInInstitutions(array $entities, $account = NULL) {
    $roles = [];
    foreach ($entities as $entity) {
      // Ignore any entities that aren't authorities or organisations.
      if (!$entity instanceof ParDataAuthority && !$entity instanceof ParDataOrganisation) {
        continue;
      }

      $members = $entity->getPerson();

      $roles[$entity->uuid()] = [];
      foreach ($members as $member) {
        /** @var \Drupal\user\Entity\User $user */
        $user = $member->getUserAccount();

        if (isset($user) && $user->isActive() && (!isset($account) || $user->id() !== $account->id())) {
          foreach ($user->getRoles() as $role) {
            $roles[$entity->uuid()][$role][] = $member;
          }
        }
      }
    }

    return $roles;
  }

  /**
   * Check whether given roles exist in any of the member's authorities.
   *
   * Allows determination of whether they are the last user with a given
   * role in any of their authorities as this affects whether they can
   * be assigned roles or even removed from the system.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check member authorities by.
   * @param array $roles
   *   An array of roles to lookup.
   *
   * @return bool
   *   If the roles don't exist in ANY authorities return false, otherwise true.
   *
   * @throws \Drupal\par_data\ParDataException
   *    If there are no authorities to lookup.
   */
  public function isRoleInAllMemberAuthorities(AccountInterface $account, array $roles) {
    $authorities = $this->isMemberOfAuthority($account);
    if (empty($authorities)) {
      throw new ParDataException('The user has no authorities, roles cannot be matched');
    }

    $authority_roles = $this->getRolesInInstitutions($authorities, $account);

    foreach ($roles as $role) {
      foreach ($authority_roles as $authority) {
        if (empty($authority[$role])) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  /**
   * Check whether given roles exist in any of the member's organisations.
   *
   * Allows determination of whether they are the last user with a given
   * role in any of their organisations as this affects whether they can
   * be assigned roles or even removed from the system.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account to check member organisations by.
   * @param array $roles
   *   An array of roles to lookup.
   *
   * @return bool
   *   If the roles don't exist in ANY organisations return false,
   *   otherwise true.
   *
   * @throws \Drupal\par_data\ParDataException
   *    If there are no organisations to lookup.
   */
  public function isRoleInAllMemberOrganisations(AccountInterface $account, array $roles) {
    $organisations = $this->isMemberOfOrganisation($account);
    if (empty($organisations)) {
      throw new ParDataException('The user has no organisations, roles cannot be matched');
    }

    $organisation_roles = $this->getRolesInInstitutions($organisations, $account);

    foreach ($roles as $role) {
      foreach ($organisation_roles as $organisation) {
        if (empty($organisation[$role])) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  /**
   * A query helper.
   */
  public function getEntityQuery($type, $conjunction = 'AND', $access_check = FALSE) {
    return $this->entityTypeManager->getStorage($type)->getQuery($conjunction)
      ->accessCheck($access_check);
  }

  /**
   * A helper function to load entity properties.
   *
   * @param string $type
   *   The entity type to load the field for.
   * @param string $field
   *   The field name.
   * @param string $value
   *   The value to load based on.
   * @param bool $deleted
   *   Return deleted files as well?
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities found with this value.
   */
  public function getEntitiesByProperty($type, $field, $value, $deleted = TRUE) {
    // Check that a value is specified.
    if (is_null($value)) {
      return [];
    }

    $entities = $this->entityTypeManager
      ->getStorage($type)
      ->loadByProperties([$field => $value]);

    // Do not return any entities that are deleted.
    // @see PAR-1462 - Removing all deleted entities from loading.
    if ($deleted) {
      $entities = array_filter($entities, fn($entity) => !$entity instanceof ParDataEntityInterface || !$entity->isDeleted());
    }

    return $entities;
  }

  /**
   * A helper function to load entity properties.
   *
   * @param string $type
   *   The entity type to load the field for.
   * @param array|null $ids
   *   An optional array of ids to load.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities found with this value.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getEntitiesByType(string $type, ?array $ids = NULL): array {
    $entities = $this->entityTypeManager
      ->getStorage($type)
      ->loadMultiple($ids);

    // Do not return any entities that are deleted.
    // @see PAR-1462 - Removing all deleted entities from loading.
    $entities = array_filter($entities, fn($entity) => !$entity instanceof ParDataEntityInterface || !$entity->isDeleted());

    return $entities;
  }

  /**
   * A helper function to build an entity query and load entities that match.
   *
   * {@inheritdoc}
   *
   * @code
   * $conditions = [
   *   [
   *     'AND/OR' => [
   *       ['field_name', $searchQuery, 'STARTS_WITH'],
   *       ['field_nothing', $searchQuery, 'IS NULL'],
   *       ['field_number', $searchQuery, '>'],
   *     ]
   *   ],
   *   [
   *     'OR' => [
   *       ['organisation_name', $searchQuery, 'CONTAINS'],
   *       ['trading_name', $searchQuery, 'ENDS_WITH']
   *     ],
   *   ],
   *   [
   *     'AND' => [
   *       ['organisation_name', $searchQuery, 'LIKE'],
   *       ['quantity', $searchQuery, '<>']
   *     ],
   *   ],
   * ];
   * @endcode
   */
  #[\Override]
  public function getEntitiesByQuery(string $type, array $conditions, $limit = NULL, $sort = NULL, $direction = 'ASC', $conjunction = 'AND', $remove_deleted_entities = TRUE): array {
    $entities = [];

    $query = $this->getEntityQuery($type, $conjunction);

    foreach ($conditions as $row) {
      foreach ($row as $condition_operator => $condition_row) {
        $group = (strtoupper((string) $condition_operator) === 'OR') ? $query->orConditionGroup() : $query->andConditionGroup();

        foreach ($condition_row as $row) {
          $group->condition(...$row);
        }

        $query->condition($group);
      }
    }

    if ($sort) {
      $query->sort($sort, $direction);
    }

    if ($limit) {
      $query->range(0, $limit);
    }

    $results = $query->execute();
    $entities = $this->entityTypeManager->getStorage($type)->loadMultiple(array_unique($results));

    // In some cases we need to return deleted entities mainly for updating
    // legacy data across the system.
    if ($remove_deleted_entities) {
      // Do not return any entities that are deleted.
      // @see PAR-1462 - Removing all deleted entities from loading.
      $entities = array_filter($entities, fn($entity) => !$entity instanceof ParDataEntityInterface || !$entity->isDeleted());
    }

    return $entities;
  }

  /**
   * Helper function to get all entities as options.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities to turn into options.
   * @param array $options
   *   An optional array of options to append to.
   * @param string $view_mode
   *   A view mode to render the entity as.
   * @param bool $access_check
   *   Whether to check all entities for access, this is an expensive operation
   *   so not enabled by default.
   *
   * @return array
   *   An array of options keyed by entity id.
   */
  public function getEntitiesAsOptions(array $entities, $options = [], $view_mode = NULL, $access_check = FALSE) {
    foreach ($entities as $entity) {
      if ($entity instanceof EntityInterface) {
        if ($entity instanceof ParDataEntityInterface &&
          ($access_check && !$entity->access('view', $this->getCurrentUser()))) {
          continue;
        }

        if ($view_mode) {
          $view_builder = $this->getViewBuilder($entity->getEntityTypeId());
          $view = $view_builder->view($entity, $view_mode);
          $options[$entity->id()] = $this->getRenderer()->renderInIsolation($view);
        }
        else {
          $options[$entity->id()] = $entity->label();
        }
      }
    }

    return $options;
  }

  /**
   * Helper function to get all entities as autocomplete options.
   *
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities to turn into options.
   * @param array $options
   *   An optional array of options to append to.
   * @param bool $access_check
   *   Whether to check all entities for access, this is an expensive operation
   *   so not enabled by default.
   *
   * @return array
   *   An array of options keyed by entity id.
   */
  public function getEntitiesAsAutocomplete($entities, $options = [], $access_check = FALSE) {
    foreach ($entities as $entity) {
      if ($entity instanceof EntityInterface) {
        if ($entity instanceof ParDataEntityInterface &&
          ($access_check && !$entity->access('view', $this->getCurrentUser()))) {
          continue;
        }

        $label = "{$entity->label()} ({$entity->id()})";
        $options[] = Tags::encode($label);
      }
    }

    return $options;
  }

  /**
   * Search for a given entity value within an entity reference field.
   *
   * Return any deltas if the entity was found.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to search for the given value.
   * @param string $field
   *   The field name to search for the given value.
   * @param mixed $value
   *   The entity id to search for.
   * @param string $property
   *   The field property to search under.
   *
   * @return array|null
   *   An array of deltas if the value was found, or otherwise null.
   */
  public function getReferenceValueKeys($entity, string $field, $value, string $property = 'target_id') {
    if (!$entity instanceof EntityInterface) {
      return NULL;
    }

    // Extract all the raw field values.
    $field_values = $entity->hasField($field) ? $entity->get($field)->getValue() : NULL;

    // Search the return values array.
    $keys = $field_values ? array_keys(array_column($field_values, $property), $value, TRUE) : NULL;

    return !empty($keys) ? $keys : NULL;
  }

  /**
   * Render an entity.
   *
   * @param string $entity_type
   *   The entity type identifier.
   * @param mixed $entity_id
   *   The unique entity identifier.
   * @param string $view_mode
   *   The view mode.
   *
   * @return array
   *   A render array.
   */
  public function renderEntityCallback(string $entity_type, $entity_id, string $view_mode) {
    $view_builder = $this->getViewBuilder($entity_type);
    $entities = $this->getEntitiesByType($entity_type, [$entity_id]);

    foreach ($entities as $entity) {
      return $view_builder->view($entity, $view_mode);
    }
    return [];
  }

  /**
   * Get the PAR People that share the same email with the user account.
   *
   * A person can be matched to a user account if:
   * a) the user id is set on the par_data_person in field_user_account
   * b) the person has no user account set (as above) but the email matches the
   *    user account email.
   *
   * @param \Drupal\user\UserInterface $account
   *   A user account.
   *
   * @return \Drupal\par_data\Entity\ParDataPersonInterface[]
   *   PAR People related to the user account.
   *
   * @see ParDataPerson::setUserAccount()
   */
  public function getUserPeople(UserInterface $account): array {
    return \Drupal::service('par_roles.role_manager')->getPeople($account);
  }

  /**
   * Get the PAR Person related to a user in the target entity.
   *
   * @param \Drupal\user\UserInterface $account
   *   The account.
   * @param \Drupal\par_data\Entity\ParDataEntityInterface $entity
   *   The authority or organisation entity to get the user for.
   *
   * @return \Drupal\par_data\Entity\ParDataPerson|null
   *   The Found Person or null.
   */
  public function getUserPerson(UserInterface $account, ParDataEntityInterface $entity) {
    $entity_people = $entity->hasField('field_person') ? $entity->retrieveEntityIds('field_person') : [];
    $user_people = $this->getUserPeople($account);

    $person_id = current(array_intersect(array_keys($user_people), $entity_people));

    return !empty($person_id) ? ParDataPerson::load($person_id) : NULL;
  }

  /**
   * Relate all PAR people that share the same email to this user account.
   *
   * @param \Drupal\user\UserInterface $account
   *   The user account to link to.
   */
  public function linkPeople(UserInterface $account) {
    foreach ($this->getUserPeople($account) as $par_person) {
      $par_person->linkAccounts($account);
    }
  }

  /**
   * Process a CSV file.
   *
   * @param \Drupal\file\FileInterface $file
   *   CSV file object.
   * @param array $rows
   *   An array to add processed rows to.
   * @param bool $skip
   *   Whether to skip the headers.
   *
   * @return array
   *   An array of row data.
   */
  public function processCsvFile(FileInterface $file, $rows = [], $skip = TRUE) {
    if (($handle = fopen($file->getFileUri(), "r")) !== FALSE) {
      while (($data = fgetcsv($handle)) !== FALSE) {
        if ($data !== NULL && !$skip) {
          $rows[] = $data;
        }
        $skip = FALSE;
      }
      fclose($handle);
    }

    return $rows;
  }

  /**
   * Calculate the average percentage completion for an entity.
   *
   * @param array $values
   *   An array of integer values.
   *
   * @return int
   *   The calculated average.
   */
  public function calculateAverage(array $values) {
    $count = count($values);
    if (!$count > 0) {
      return 0;
    }

    $sum = array_sum($values);
    $median = $sum / $count;
    $average = ceil($median);

    return $average;
  }

  /**
   * Generates foreign keys to assist with generation of database diagram.
   *
   * SHOULD ONLY BE USED IN DEVELOPMENT ENVIRONMENTS, NEVER ON PROD.
   *
   * @see https://regulatorydelivery.atlassian.net/wiki/spaces/PA/pages/324435969/Drupal+Data+Model
   */
  public static function generateForeignKeys() {
    if (getenv('APP_ENV') == 'production') {
      return;
    }

    $par_data_manager = \Drupal::service('par_data.manager');

    $queries = [];
    foreach ($par_data_manager->getParEntityTypes() as $entity_type_id => $entity_type) {
      $key_prefix = 'fk_';
      $base_table = $entity_type->getBaseTable();
      $field_table = $entity_type->getDataTable();

      // Add entity base field relationships.
      $queries[] = <<<EOT
ALTER TABLE {$field_table}
  ADD CONSTRAINT {$key_prefix}{$field_table}___{$base_table}
FOREIGN KEY (id)
REFERENCES {$base_table}(id);
EOT;

      // For reference fields that are not mandatory they use a value for 0
      // for the target_id which is not a valid foreign key. Insert an arbitrary
      // value.
      $qry = <<<EOT
      INSERT into {$base_table}
      VALUES (0, 9999999999, 'bogus', '4b7f74dc-ae10-unkn-own-29207b47797a', 'en');
EOT;
      // And make sure it runs first.
      array_unshift($queries, $qry);

      // Get all reference fields and add their relationships.
      $bundle = $par_data_manager->getParBundleEntity($entity_type_id);
      $references = $par_data_manager->getReferences($entity_type_id, $bundle->id());
      foreach ($references as $field_entity_type_id => $fields) {
        // If the reference is on the current entity type
        // we can get the value from the current $entity.
        if ($entity_type_id === $field_entity_type_id) {
          foreach ($fields as $field_name => $field) {
            $storage = $field->getFieldStorageDefinition();
            $target_type_id = $field->getSetting('target_type');
            if ($entity_type_id === $target_type_id) {
              continue;
            }

            $target_type = $par_data_manager->getParEntityType($target_type_id);

            $table_mapping = new DefaultTableMapping($entity_type, [$storage]);
            $target = $table_mapping->getFieldColumnName($storage, 'target_id');
            $table_name = $table_mapping->getDedicatedDataTableName($storage);

            $queries[] = <<<EOT
ALTER TABLE {$table_name}
  ADD CONSTRAINT {$key_prefix}{$table_name}___{$target}
FOREIGN KEY ({$target})
REFERENCES {$target_type->getBaseTable()}(id);
EOT;
            $queries[] = <<<EOT
ALTER TABLE {$table_name}
  ADD CONSTRAINT {$key_prefix}{$table_name}___entity_id
FOREIGN KEY (entity_id)
REFERENCES {$base_table}(id);
EOT;
          }
        }
      }
    }

    $connection = \Drupal::database();
    foreach ($queries as $i => $query) {
      try {
        $executed = $connection->query($query);
      }
      catch (\Exception $e) {
        var_dump($e->getMessage());
      }
    }
  }

  /**
   * Debugging function for introspecting all reference fields on an entity type.
   */
  public function buildReferenceTree($entity_types = [], $references = []) {
    if (empty($entity_types)) {
      $entity_types = $this->getParEntityTypes();
    }
    if ($this->debug) {
      $debug_tree = '';
    }

    foreach ($entity_types as $entity_type_id => $entity_type) {
      $definition = $entity_type ? $this->getEntityBundleDefinition($entity_type) : NULL;
      $bundles = $definition ? $this->getEntityTypeStorage($definition->id())->loadMultiple() : [];

      foreach ($bundles as $bundle) {
        $current_references = $this->getReferences($entity_type_id, $bundle->id());
        // Only use the reverse references.
        $current_references = array_filter($current_references, fn($relationship) => $relationship->getRelationshipDirection() === ParDataRelationship::DIRECTION_REVERSE);

        // Allow a debug tree to be built.
        if ($this->debug) {
          $debug_tree .= $entity_type_id . PHP_EOL;
          $debug_tree .= '&mdash;' . $bundle->id() . PHP_EOL;
          if (isset($current_references[$entity_type_id])) {
            foreach ($current_references[$entity_type_id] as $reference) {
              $debug_tree .= '&mdash;&mdash;' . $reference->getsetting('target_type') . PHP_EOL;
            }
          }
        }

        $references += $current_references;
      }
    }

    // Output debugging.
    if ($this->debug) {
      $tree = Markup::create(nl2br("<br>" . $debug_tree));
      $this->getMessenger()->addMessage(t('New relationship tree: @tree', ['@tree' => $tree]));
    }

    return $references;
  }

}
