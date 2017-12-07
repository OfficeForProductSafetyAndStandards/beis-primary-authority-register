<?php

namespace Drupal\par_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TranceAddController.
 *
 * @package Drupal\par_data\Controller
 */
class ParDataAddController extends ControllerBase {

  /**
   * Constructor.
   */
  public function __construct(EntityStorageInterface $storage, EntityStorageInterface $type_storage) {
    $this->storage = $storage;
    $this->typeStorage = $type_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    $entity_type = \Drupal::routeMatch()->getParameter('par_data_entity');
    $entity_definition = $entity_type ? $entity_manager->getDefinition($entity_type) : NULL;

    $entity_storage = $entity_definition ? $entity_manager->getStorage($entity_definition->id()) : NULL;
    $entity_type_storage = $entity_definition ? $entity_manager->getStorage($entity_definition->getBundleEntityType()) : NULL;

    return new static(
      $entity_storage,
      $entity_type_storage
    );
  }

  /**
   * Displays add links for available bundles/types for entity trance .
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return array
   *   A render array for a list of the trance bundles/types that can be added
   *   or if there is only one type/bunlde defined for the site, the function
   *   returns the add page for that bundle/type.
   */
  public function add(Request $request) {
    $entity_type = $this->storage->getEntityType()->id();
    $bundle_entity_type = $this->storage->getEntityType()->getBundleEntityType();
    $types = $this->typeStorage->loadMultiple();
    if ($types && count($types) == 1) {
      $type = key($types);
      return $this->addForm($type, $request);
    }
    if (count($types) === 0) {
      return [
        '#markup' => $this->t('You have not created any %bundle types yet. @link to add a new type.', [
          '%bundle' => $entity_type,
          '@link' => $this->l($this->t('Go to the type creation page'), Url::fromRoute('entity.' . $bundle_entity_type . '.add_form')),
        ]),
      ];
    }
    return ['#theme' => 'trance_content_add_list', '#content' => $types];
  }

  /**
   * Presents the creation form for trance entities of given bundle/type.
   *
   * @param EntityInterface $par_data_type
   *   The custom bundle to add.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return array
   *   A form array as expected by drupal_render().
   */
  public function addForm($par_data_entity_type, Request $request) {
    $entity = $this->storage->create(['type' => $par_data_entity_type]);
    return $this->entityFormBuilder()->getForm($entity);
  }

  /**
   * Provides the page title for this controller.
   *
   * @param EntityInterface $par_data_type
   *   The custom bundle/type being added.
   *
   * @return string
   *   The page title.
   */
  public function getAddFormTitle(EntityInterface $par_data_type) {
    return $this->t('Create @label of @type', [
      '@label' => $par_data_type->label(),
      '@type' => $this->storage->getEntityType()->id(),
    ]);
  }

}
