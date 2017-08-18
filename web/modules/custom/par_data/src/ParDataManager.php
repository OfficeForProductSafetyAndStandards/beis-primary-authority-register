<?php

namespace Drupal\par_data;

use Drupal\clamav\Config;
use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
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
  protected $nonMembershipEntities = ['par_data_sic_codes', 'par_data_regulatory_function', 'par_data_advice', 'par_data_inspection_plan'];

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity bundle info service.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityManager = $entity_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
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
    $bundles = $definition ? $this->getEntityTypeStorage($definition)->loadMultiple() : [];
    return $bundles && isset($bundles[$bundle]) ? $bundles[$bundle] : current($bundles);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeStorage(EntityTypeInterface $definition) {
    return $this->entityManager->getStorage($definition->id()) ?: NULL;
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
   * Get the people related to a given entity.
   *
   * @param $entity
   * @param array $people
   * @return array
   */
  public function getRelatedPeople($entity, $people = [], $iteration = 0) {
    if (!$entity instanceof ParDataEntityInterface) {
      return $people;
    }

    // Make sure the entity isn't too distantly related
    // to limit recursive relationships.
    if ($iteration > 5) {
      return $people;
    }
    else {
      $iteration++;
    }

    // If this entity is a person we want to do nothing.
    if ($entity->getEntityTypeId() === 'par_data_person') {
      return $people;
    }
    // If this entity is a core entity we can return the related
    // person and stop looking.
    else if (in_array($entity->getEntityTypeId(), $this->coreEntities)) {
      $people += $entity->getRelationships('par_data_person');
    }
    else {
      $relationships = $entity->getRelationships();
      foreach($entity->getRelationships() as $referenced_entity) {
        if ($entity->getEntityType()->id() !== 'par_data_person') {
          $people = $this->getRelatedPeople($referenced_entity, $people, $iteration);
        }
      }
    }

    return array_filter($people);
  }

  /**
   * Get the entities related to each other.
   *
   * Follows some rules to make sure it doesn't go round in loops.
   *
   * @param $entity
   * @param array $entities
   * @param int $iteration
   * @param bool $force_lookup
   *   Force the lookup of relationships that would otherwise be ignored.
   *
   * @return EntityInterface[]
   *   An array of entities keyed by entity type.
   */
  public function getRelatedEntities($entity, $entities = [], $iteration = 0, $force_lookup = FALSE) {
    if (!$entity instanceof ParDataEntityInterface) {
      return $entities;
    }

    // Make sure the entity isn't too distantly related
    // to limit recursive relationships.
    if ($iteration > 5) {
      return $entities;
    }
    else {
      $iteration++;
    }

    // Make sure not to count the same entity again.
    if (isset($entities[$entity->getEntityTypeId()]) && isset($entities[$entity->getEntityTypeId()][$entity->id()])) {
      return $entities;
    }

    // Add this entity to the related entities.
    if (!isset($entities[$entity->getEntityTypeId()])) {
      $entities[$entity->getEntityTypeId()] = [];
    }
    $entities[$entity->getEntityTypeId()][$entity->id()] = $entity;

    // Loop through all relationships
    foreach ($entity->getRelationships() as $entity_type => $referenced_entities) {
      foreach ($referenced_entities as $entity_id => $referenced_entity) {
        // Skip lookup of relationships for people.
        if ($referenced_entity->getEntityTypeId() === 'par_data_person') {
          continue;
        }
        
        //If the related entity is a person we don't want to get
        // If the current entity is a person only lookup core entity relationships.
        if ($entity->getEntityTypeId() === 'par_data_person') {
          if (in_array($referenced_entity->getEntityTypeId(), $this->coreMembershipEntities)) {
            $entities = $this->getRelatedEntities($referenced_entity, $entities, $iteration, TRUE);
          }
        }
        // If the current entity is a core entity only lookup entity relationships
        // if forced to do so, by the person lookup.
        else if (in_array($entity->getEntityTypeId(), $this->coreMembershipEntities)) {
          if ($force_lookup) {
            $entities = $this->getRelatedEntities($referenced_entity, $entities, $iteration);
          };
        }
        // For all other entities follow your hearts content and find all
        // entity relationships..
        else if (!in_array($entity->getEntityTypeId(), $this->nonMembershipEntities)) {
          $entities = $this->getRelatedEntities($referenced_entity, $entities, $iteration);
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
   * @return bool
   */
  public function isMember($entity, UserInterface $account) {
    $entity_people = $this->getRelatedPeople($entity);

    // All access checks are done using the relationship between a user account
    // and a par person entity, so we need all the user's par people.
    if ($entity_people) {
      $account_people = $this->getUserPeople($account);
    }
    else {
      $account_people = [];
    }

    return !empty(array_intersect_key($entity_people, $account_people));
  }

  public function hasMemberships(UserInterface $account, $type = NULL) {
    $account_people = $this->getUserPeople($account);

    $memberships = [];
    foreach ($account_people as $person) {
      $memberships = array_merge_recursive($memberships, $this->getRelatedEntities($person));
    }

    return isset($type) && isset($memberships[$type]) ? $memberships[$type] : $memberships;
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
    return \Drupal::entityTypeManager()
      ->getStorage($type)
      ->loadByProperties([$field => $value]);
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
    return \Drupal::entityTypeManager()
      ->getStorage('par_data_person')
      ->loadByProperties(['email' => $account->get('mail')->getString()]);
  }

  /**
   * Relate all PAR people that share the same email to this user account.
   *
   * @param UserInterface $account
   *   The user account to link to.
   */
  public function linkPeople(UserInterface $account) {
    foreach ($this->getUserPeople($account) as $par_person) {
      $par_person->linkAccounts();
    }
  }

  /**
   * Get the available options for regulatory functions.
   *
   * @return array
   *   An array of options keyed by ID.
   */
  public function getRegulatoryFunctionsAsOptions() {
    $options = [];
    $storage = $this->getParEntityType('par_data_regulatory_function');
    foreach ($this->getEntityTypeStorage($storage)->loadMultiple() as $function) {
      $options[$function->id()] = $function->get('function_name')->getString();
    }

    return $options;
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
