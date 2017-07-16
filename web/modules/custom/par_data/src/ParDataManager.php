<?php

namespace Drupal\par_data;

use Drupal\Core\Entity\EntityManagerInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParDataManager {

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
  * {{inherrit}}
  */
  public function getParEntityTypes() {
    $par_entity_prefix = 'par_';
    $par_entity_types = [];
    $entity_type_definitions = $this->entityManager->getDefinitions();
    foreach ($entity_type_definitions as $definition) {
      if ($definition instanceof \Drupal\Core\Entity\ContentEntityType
        && substr($definition->getBundleEntityType(), 0, strlen($par_entity_prefix)) === $par_entity_prefix
      ) {
        $par_entity_types[] = $definition;
      }
    }
    return $par_entity_types ?: [];
  }

}
