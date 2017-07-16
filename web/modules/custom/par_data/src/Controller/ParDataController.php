<?php

namespace Drupal\par_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\par_data\ParDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* A controller for all styleguide page output.
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
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_data.manager'));
  }

  /**
  * The main index page for the styleguide.
  */
  public function content() {

    $build = [
      '#markup' => '<p>Here you will be able to access all of the PAR 3 data.</p>',
    ];

    return $build;
  }

}
