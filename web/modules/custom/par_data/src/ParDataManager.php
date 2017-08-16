<?php

namespace Drupal\par_data;

use Drupal\clamav\Config;
use Drupal\Core\Config\Entity\ConfigEntityType;
use Drupal\Core\Entity\EntityManagerInterface;
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
   * The core entity types through which all membership is calculated.
   */
  protected $coreEntities = ['par_data_authority', 'par_data_organisation'];

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
  * @inheritdoc}
  */
  public function getParEntityTypes() {
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
   * Get the par person records related to a given entity.
   *
   * @param $entity
   * @param array $people
   * @return array
   */
  public function getRelatedPeople($entity, $people = []) {
    if (!$entity instanceof ParDataEntityInterface) {
      return $people;
    }

    if (in_array($entity->getEntityType()->id(), $this->coreEntities)) {
      $people += $entity->getReferenceEntitiesByType('par_data_person');
    }
    else {
      foreach($entity->getReferenceFields() as $field_name => $fields) {
        foreach ($fields->referencedEntities() as $referenced_entity) {
          if ($entity->getEntityType()->id() !== 'par_data_person') {
            $people = $this->getRelatedPeople($referenced_entity, $people);
          }
        }
      }
    }

    return $people;
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
