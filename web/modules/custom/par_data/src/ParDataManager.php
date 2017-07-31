<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeInterface;
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
  public function getEntityTypeStorage(EntityTypeInterface $definition) {
    return $this->entityManager->getStorage($definition->id()) ?: NULL;
  }

  public function getUserPeople(UserInterface $account) {
    return \Drupal::entityTypeManager()
      ->getStorage('par_data_person')
      ->loadByProperties(['email' => $account->get('mail')->getString()]);
  }

  public function linkPeople(UserInterface $account) {
    foreach ($this->getUserPeople($account) as $par_person) {
      $par_person->linkAccounts();
    }
  }

}
