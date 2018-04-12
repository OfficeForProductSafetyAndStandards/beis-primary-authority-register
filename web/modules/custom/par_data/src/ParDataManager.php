<?php

namespace Drupal\par_data;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParDataManager implements ParDataManagerInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

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
   * The core entity types through which all membership is calculated.
   */
  protected $coreMembershipEntities = ['par_data_authority', 'par_data_organisation'];

  /**
   * The non membership entities from which references should not be followed.
   */
  protected $nonMembershipEntities = [
    'par_data_sic_codes',
    'par_data_regulatory_function',
    'par_data_advice',
    'par_data_inspection_plan',
    'par_data_premises',
    'par_data_legal_entity',
    'par_data_enforcement_notice',
    'par_data_enforcement_action',
  ];

  /**
   * Iteration limit for recursive membership lookups.
   */
  protected $membershipIterations = 5;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity bundle info service.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityManager = $entity_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
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
  * @inheritdoc}
  */
  public function getParEntityTypes() {
    // We're obviously assuming that all par entities begin with this prefix.
    $par_entity_prefix = 'par_data_';
    $par_entity_types = [];
    $entity_type_definitions = $this->entityManager->getDefinitions();
    foreach ($entity_type_definitions as $definition) {
      if ($definition instanceof ContentEntityType
        && substr($definition->getBundleEntityType(), 0, strlen($par_entity_prefix)) === $par_entity_prefix
      ) {
        $par_entity_types[$definition->id()] = $definition;
      }
    }
    return $par_entity_types ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getParEntityType(string $type) {
    $types = $this->getParEntityTypes();
    return isset($types[$type]) ? $types[$type] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityBundleDefinition(EntityTypeInterface $definition) {
    return $definition->getBundleEntityType() ? $this->entityManager->getDefinition($definition->getBundleEntityType()) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getParBundleEntity(string $type, $bundle = NULL) {
    $entity_type = $this->getParEntityType($type);
    $definition = $entity_type ? $this->getEntityBundleDefinition($entity_type) : NULL;
    $bundles = $definition ? $this->getEntityTypeStorage($definition->id())->loadMultiple() : [];
    return $bundles && isset($bundles[$bundle]) && !empty($bundles[$bundle]) ? $bundles[$bundle] : current($bundles);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeStorage($definition) {
    return $this->entityManager->getStorage($definition) ?: NULL;
  }

  /**
   * {@inhertidoc}
   */
  public function getViewBuilder($entity_type) {
    return $this->entityTypeManager->getViewBuilder($entity_type);
  }

  /**
   * Get a field definition.
   */
  public function getFieldDefinition($entity_type, $bundle, $field) {
    $entity_fields = $this->entityFieldManager->getFieldDefinitions($entity_type, $bundle);
    return isset($entity_fields[$field]) ? $entity_fields[$field] : NULL;
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
    $field_definition = $this->getFieldDefinition($entity_type, $bundle, $field);
    return $field_definition ? $field_definition->getDefaultValueLiteral() : [];
  }

  /**
   * Get all references for a given entity.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of field definitions keyed by the entity type they are attached to.
   */
  public function getReferences($type, $bundle) {
    $reference_fields = [];

    // First get all the entities referenced by this entity type.
    foreach ($this->entityFieldManager->getFieldDefinitions($type, $bundle) as $field_name => $definition) {
      if ($definition->getType() === 'entity_reference' && $this->getParEntityType($definition->getsetting('target_type'))) {
        $reference_fields[$type][$field_name] = $definition;
      }
    }

    // Second get all the entities that reference this entity type.
    $entity_references = $this->entityFieldManager->getFieldMapByFieldType('entity_reference');
    foreach ($entity_references as $referring_entity_type => $fields){
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
   * @param $entity
   *   The entity being looked up.
   * @param array $entities
   *   The entities relationship tree.
   * @param array $processedEntities
   *   The hash table of entities that have already been processed, includes those ignored in the entity relationship tree.
   * @param int $iteration
   *   The depth of relationships to go before stopping.
   * @param bool $force_lookup
   *   Force the lookup of relationships that would otherwise be ignored.
   *
   * @return EntityInterface[][]
   *   An array of entities keyed by entity type.
   */

  public function getRelatedEntities($entity, $entities = [], &$processedEntities = [], $iteration = 0, $force_lookup = FALSE) {
    if (!$entity instanceof ParDataEntityInterface) {
      return $entities;
    }

    // Make sure the entity isn't too distantly related
    // to limit recursive relationships.
    if ($iteration >= $this->membershipIterations) {
      return $entities;
    }
    else {
      $iteration++;
    }

    // Make sure not to count the same entity again.
    $entityHashKey = $entity->getEntityTypeId() . ':' . $entity->id();
    if (isset($processedEntities[$entityHashKey])) {
      return $entities;
    }

    // Add hash key to show this node has been processed, whether or not it is used or ignored
    $processedEntities[$entityHashKey] = true;

    // Add this entity to the related entities.
    if (!isset($entities[$entity->getEntityTypeId()])) {
      $entities[$entity->getEntityTypeId()] = [];
    }
    $entities[$entity->getEntityTypeId()][$entity->id()] = $entity;

    // Everything after this point is costly and we can cache.
    $cache = \Drupal::cache('data')->get("par_data_relationships:{$entityHashKey}");
    if ($cache) {
      $relationships = $cache->data;
    }
    else {
      // Now we've decided if it's worth processing this entity let's cache it.
      $relationships = $entity->getRelationships();

      // Set cache tags for all these relationships.
      $tags[] = $entityHashKey;
      foreach ($relationships as $entity_type => $referenced_entities) {
        foreach ($referenced_entities as $referenced_entity_id => $referenced_entity) {
          $tags[] = $entity_type . ':' . $referenced_entity->id();
        }
      }

      \Drupal::cache('data')->set("par_data_relationships:{$entityHashKey}", $relationships, Cache::PERMANENT, $tags);
    }

    // Loop through all relationships.
    foreach ($relationships as $entity_type => $referenced_entities) {

      // @TODO PAR-1025: This is a temporary fix to resolve performance issues
      // with looking up the large numbers of premises.
      if ($entity_type === 'par_data_premises') {
        continue;
      }

      foreach ($referenced_entities as $entity_id => $referenced_entity) {
        // Always skip lookup of relationships for people.
        if ($referenced_entity->getEntityTypeId() === 'par_data_person') {
          continue;
        }

        // If the current entity is a person only lookup core entity relationships.
        if ($entity->getEntityTypeId() === 'par_data_person') {
          if (in_array($referenced_entity->getEntityTypeId(), $this->coreMembershipEntities)) {
            $entities = $this->getRelatedEntities($referenced_entity, $entities, $processedEntities, $iteration, TRUE);
          }
        }
        // If the current entity is a core entity only lookup entity relationships
        // if forced to do so, by the person lookup.
        else if (in_array($entity->getEntityTypeId(), $this->coreMembershipEntities)) {
          if ($force_lookup) {
            $entities = $this->getRelatedEntities($referenced_entity, $entities, $processedEntities, $iteration);
          };
        }
        // For all other entities follow your hearts content and find all
        // entity relationships..
        else if (!in_array($entity->getEntityTypeId(), $this->nonMembershipEntities)) {
          $entities = $this->getRelatedEntities($referenced_entity, $entities, $processedEntities, $iteration);
        }
      }
    }

    return $entities;
  }

  /**
   * Determine whether a user account is a member of any given entity.
   *
   * @param EntityInterface $entity
   *   An entity to check membership on.
   * @param UserInterface $account
   *   A user account to check for.
   *
   * @return bool
   *   Returns whether the account is a part of a given entity.
   */
  public function isMember($entity, UserInterface $account) {
    return isset($this->hasMembershipsByType($account, $entity->getEntityTypeId())[$entity->id()]);
  }

  /**
   * Determine which entities a user is a part of.
   *
   * @TODO Sorry, couldn't fix this before the support contract was pulled
   * but this method is inefficient and needs re-writting. There are much
   * better ways of calculating which entity actions can be performed.
   * @see https://regulatorydelivery.atlassian.net/wiki/spaces/PA/pages/40894490/Par+Access+Actions
   *
   * @param UserInterface $account
   *   A user account to check for.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return array
   *   Returns an array of entities keyed by entity type and then by entity id.
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
    //  entities, i.e. authorities or organisations.
    $object = $direct ? $this->getReducedIterator(2) : $this;

    $memberships = [];
    $hash_tree = [];
    foreach ($account_people as $person) {
      $relationships = $object->getRelatedEntities($person, $memberships, $hash_tree);
      foreach ($relationships as $entity_type => $entities) {
        if (!isset($memberships[$entity_type])) {
          $memberships[$entity_type] = [];
        }
        $memberships[$entity_type] += $entities;
      }
    }

    return !empty($memberships) ? $memberships : [];
  }

  /**
   * Determine which entities of a given type the user is part of.
   *
   * @param UserInterface $account
   *   A user account to check for.
   * @param string $type
   *   An entity type to filter on the return on.
   * @param bool $direct
   *   Whether to check only direct relationships.
   *
   * @return array
   *   Returns the entities for the given type.
   */
  public function hasMembershipsByType(UserInterface $account, $type, $direct = FALSE) {
    $memberships = $this->hasMemberships($account, $direct);

    return $memberships && isset($memberships[$type]) ? $memberships[$type] : [];
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
    return $account ? $this->hasMembershipsByType($account, 'par_data_organisation',  TRUE) : NULL;
  }

  /**
   * Helper function to get all the roles filled within a users's authorities.
   *
   * @param ParDataEntityInterface[] $entities
   *   The authority entities to look for roles in.
   * @param AccountInterface $account
   *   If a user is passed their roles will be ignored.
   *
   * @return array
   *   An array of member's keyed by authority and then role id.
   */
  public function getRolesInAuthorities(array $entities, $account = NULL) {
    $roles = [];
    foreach ($entities as $authority) {
      $members = $authority->getPerson();

      $roles[$authority->uuid()] = [];
      foreach ($members as $member) {
        $user = $member->getUserAccount();

        if (isset($user) && (!isset($account) || $user->id() !== $account->id())) {
          foreach ($user->getRoles() as $role) {
            $roles[$authority->uuid()][$role][] = $member;
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
   * @param AccountInterface $account
   *   The user account to check member authorities by.
   * @param $roles
   *   An array of roles to lookup.
   *
   * @return bool
   *   If the roles exist in ALL authorities return true, otherwise false.
   */
  public function isRoleInAllMemberAuthorities(AccountInterface $account, $roles) {
    $authorities = $this->isMemberOfAuthority($account);
    $authority_roles = $this->getRolesInAuthorities($authorities, $account);

    foreach ($roles as $role) {
      foreach ($authority_roles as $authority) {
        if (!isset($authority[$role]) || empty($authority[$role])) {
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
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities found with this value.
   */
  public function getEntitiesByProperty($type, $field, $value) {
    // Check that a value is specified.
    if (is_null($value)) {
      return [];
    }

    return $this->entityTypeManager
      ->getStorage($type)
      ->loadByProperties([$field => $value]);
  }

  /**
   * A helper function to load entity properties.
   *
   * @param string $type
   *   The entity type to load the field for.
   * @param array $ids
   *   An optional array of ids to load.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities found with this value.
   */
  public function getEntitiesByType($type, array $ids = NULL) {
    return $this->entityManager
      ->getStorage($type)
      ->loadMultiple($ids);
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
  public function getEntitiesByQuery(string $type, array $conditions, $limit = NULL, $sort = NULL, $direction = 'ASC') {
    $entities = [];

    foreach ($conditions as $row) {
      $query = $this->getEntityQuery($type);

      foreach ($row as $condition_operator => $condition_row) {
        $group = (strtoupper($condition_operator) === 'OR') ? $query->orConditionGroup() : $query->andConditionGroup();

        foreach ($condition_row as $row) {
          $group->condition(...$row);
        }

        $query->condition($group);

        if ($limit) {
          $query->range(0, $limit);
        }

        $entities += $query->execute();
      }
    }

    return $this->entityManager->getStorage($type)->loadMultiple(array_unique($entities));
  }

  /**
   * Helper function to get all entities as options.
   *
   * @param EntityInterface $entities
   *   An array of entities to turn into options.
   * @param array $options
   *   An optional array of options to append to.
   *
   * @return []
   *   An array of options keyed by entity id.
   */
  public function getEntitiesAsOptions($entities, $options = []) {
    foreach ($entities as $entity) {
      if ($entity instanceof EntityInterface) {
        $options[$entity->id()] = $entity->label();
      }
    }

    return $options;
  }

  /**
   * Get the PAR People that share the same email with the user account.
   *
   * @param UserInterface $account
   *   A user account.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   PAR People related to the user account.
   */
  public function getUserPeople(UserInterface $account) {
    return $this->entityTypeManager
      ->getStorage('par_data_person')
      ->loadByProperties(['email' => $account->get('mail')->getString()]);
  }

  /**
   * Get the PAR Person related to a user in the target entity.
   *
   * @param UserInterface $account
   *   The account
   * @param ParDataEntityInterface $entity
   *   The authority or organisation entity to get the user for.
   *
   * @return ParDataPerson
   */
  public function getUserPerson($account, $entity) {
    $entity_people = $entity->hasField('field_person') ? $entity->retrieveEntityIds('field_person') : [];
    $user_people = $this->getUserPeople($account);

    $person_id = current(array_intersect_key(array_keys($user_people), array_keys($entity_people)));
    return !empty($person_id) ? ParDataPerson::load($person_id) : NULL;
  }

  /**
   * Relate all PAR people that share the same email to this user account.
   *
   * @param UserInterface $account
   *   The user account to link to.
   */
  public function linkPeople(UserInterface $account) {
    foreach ($this->getUserPeople($account) as $par_person) {
      $par_person->linkAccounts($account);
    }
  }

  /**
   * Process a CSV file
   *
   * @param FileInterface $file
   * @param array $rows
   *   An array to add processed rows to.
   * @param boolean $skip
   *   Whether to skip the headers.
   *
   * @return array
   *   An array of row data.
   */
  public function processCsvFile(FileInterface $file, $rows = [], $skip = TRUE) {
    // Need to set auto_detect_line_endings to deal with Mac line endings.
    // @see http://php.net/manual/en/function.fgetcsv.php
    ini_set('auto_detect_line_endings', TRUE);

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
   * @return integer
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

}
