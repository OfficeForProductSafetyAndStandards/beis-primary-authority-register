<?php

namespace Drupal\par_data\Permissions;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParDataPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   */
  public function __construct(ParDataManagerInterface $par_data_manager) {
    $this->parDataManager = $par_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_data.manager'));
  }

  /**
   * Get permissions for Taxonomy Views Integrator.
   *
   * @return array
   *   Permissions array.
   */
  public function permissions() {
    $permissions = [];

    foreach ($this->parDataManager->getParEntityTypes() as $definition) {
      $type = $definition->getBundleEntityType();
      $name = $definition->getLabel();

      // View.
      $permissions += [
        "access {$type} entities" => [
          'title' => $this->t('Allow access to %entity entities', array('%entity' => $name)),
        ]
      ];

      // View archived.
      $permissions += [
        "view unpublished {$type} entities" => [
          'title' => $this->t('Allow access to archived %entity entities', array('%entity' => $name)),
        ]
      ];

      // Add an administration permission.
      $permissions += [
        "administer {$type} entities" => [
          'title' => $this->t('Allow administration and configuration of %entity entities', array('%entity' => $name)),
        ]
      ];

      // Add an administration permission for the entities bundles.
      $permissions += [
        "administer {$type} entity types" => [
          'title' => $this->t('Allow administration and configuration of %entity entity sub-types', array('%entity' => $name)),
        ]
      ];

      // By pass all access checks for this entity.
      $permissions += [
        "bypass {$type} access" => [
          'title' => $this->t('Allow administration and configuration of %entity entities', array('%entity' => $name)),
        ]
      ];

      // Create.
      $permissions += [
        "add {$type} entities" => [
          'title' => $this->t('Create new %entity entities', array('%entity' => $name)),
        ]
      ];

      // Update.
      $permissions += [
        "edit {$type} entities" => [
          'title' => $this->t('Update %entity entities', array('%entity' => $name)),
        ]
      ];

      // Delete.
      $permissions += [
        "delete {$type} entities" => [
          'title' => $this->t('Delete %entity entities', array('%entity' => $name)),
        ]
      ];
    }

    return $permissions;
  }

}
