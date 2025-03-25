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
  #[\Override]
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
      $id = $definition->id();
      $plural = $definition->getPluralLabel();

      // View.
      $permissions += [
        "access {$id} entities" => [
          'title' => $this->t('Allow access to %entity', ['%entity' => $plural]),
        ]
      ];

      // View archived.
      $permissions += [
        "view unpublished {$id} entities" => [
          'title' => $this->t('Allow access to archived %entity', ['%entity' => $plural]),
        ]
      ];

      // Add an administration permission for bundles.
      $permissions += [
        "administer {$id} entities" => [
          'title' => $this->t('Allow administration and configuration of %entity', ['%entity' => $plural]),
        ]
      ];

      // Add an administration permission for the entities types.
      $permissions += [
        "administer {$id} entity types" => [
          'title' => $this->t('Allow administration and configuration of %entity sub-types', ['%entity' => $plural]),
        ]
      ];

      // By pass all access checks for this entity.
      $permissions += [
        "bypass {$id} access" => [
          'title' => $this->t('Allow administration and configuration of %entity', ['%entity' => $plural]),
        ]
      ];

      // Create.
      $permissions += [
        "add {$id} entities" => [
          'title' => $this->t('Create new %entity', ['%entity' => $plural]),
        ]
      ];

      // Update.
      $permissions += [
        "edit {$id} entities" => [
          'title' => $this->t('Update %entity', ['%entity' => $plural]),
        ]
      ];

      // Delete.
      $permissions += [
        "delete {$id} entities" => [
          'title' => $this->t('Delete %entity', ['%entity' => $plural]),
        ]
      ];
    }

    return $permissions;
  }

}
