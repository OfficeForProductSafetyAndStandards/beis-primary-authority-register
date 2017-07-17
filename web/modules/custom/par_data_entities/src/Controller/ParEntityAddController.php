<?php

namespace Drupal\par_data_entities\Controller;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\trance\Controller\TranceAddController;

/**
 * Class ParEntityAddController.
 *
 * @package Drupal\par_data_entities\Controller
 */
class ParEntityAddController extends TranceAddController {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('par_entity'),
      $entity_manager->getStorage('par_entity_type')
    );
  }

  /**
   * Presents the creation form for par_entity entities of given bundle.
   *
   * @param EntityInterface $par_entity_type
   *   The custom bundle to add.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function addForm(EntityInterface $par_entity_type, Request $request) {
    return parent::addForm($par_entity_type, $request);
  }

  /**
   * Provides the page title for this controller.
   *
   * @param EntityInterface $par_entity_type
   *   The custom bundle/type being added.
   *
   * @return string
   *   The page title.
   */
  public function getAddFormTitle(EntityInterface $par_entity_type) {
    return parent::getAddFormTitle($par_entity_type);
  }

}
