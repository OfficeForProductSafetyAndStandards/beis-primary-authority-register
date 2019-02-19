<?php

namespace Drupal\par_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
* A controller listing all par reference fields and their purpose.
*/
class ParDataReferencesController extends ControllerBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $parDataManager;

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
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ParDataManagerInterface $par_data_manager, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->parDataManager = $par_data_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_data.manager'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
  * The main page for listing par entities.
  */
  public function content() {
    $build = [];

    $references = $this->parDataManager->buildReferenceTree();

    foreach ($references as $entity_type => $fields) {
      // Display the Entity type.
      $build['references'][$entity_type] = [
        '#type' => 'fieldset',
        '#title' => $entity_type,
        '#attributes' => ['class' => ['form-group']],
      ];

      foreach ($fields as $field_name => $reference) {
        // Display the Entity type.
        $build['references'][$entity_type]['from'] = [
          '#theme' => 'markup',
          '#markup' => nl2br($field_name . ': ' . $reference->getDescription() . PHP_EOL),
        ];
        $build['references'][$entity_type][$field_name] = [
          '#theme' => 'markup',
          '#markup' => nl2br($field_name . ': ' . $reference->getDescription() . PHP_EOL),
        ];
      }
    }

    return $build;
  }

}
