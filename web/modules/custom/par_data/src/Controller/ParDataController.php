<?php

namespace Drupal\par_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
* A controller managing par data admin pages.
*/
class ParDataController extends ControllerBase {

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
  * The main page for listing par entities.
  */
  public function content() {
    $types = [];

    foreach ($this->parDataManager->getParEntityTypes() as $definition) {
      $types[] = [
        'url' => Url::fromRoute("entity.{$definition->id()}.collection"),
        'label' => $definition->getPluralLabel()->render(),
        'description' => $this->t('Please visit to configure this Par Data Type.'),
      ];
    }

    return ['#theme' => 'par_data_entity_list', '#types' => $types];
  }

  /**
   * The main par for configuring par entities.
   */
  public function structure() {
    $types = [];

    foreach ($this->parDataManager->getParEntityTypes() as $definition) {
      $types[] = [
        'url' => Url::fromRoute("entity.{$definition->getBundleEntityType()}.collection"),
        'label' => $definition->getPluralLabel()->render(),
        'description' => $this->t('Please visit to configure this Par Data Type.'),
      ];
    }

    return ['#theme' => 'par_data_entity_list', '#types' => $types];
  }

}
