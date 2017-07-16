<?php

namespace Drupal\example\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic routes.
 */
class ExampleRoutes implements ContainerInjectionInterface {

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
   * {@inheritdoc}
   *
   * new Route()...
   * @param string       $path         The path pattern to match
   * @param array        $defaults     An array of default parameter values
   * @param array        $requirements An array of requirements for parameters (regexes)
   * @param array        $options      An array of options
   * @param string       $host         The host pattern to match
   * @param string|array $schemes      A required URI scheme or an array of restricted schemes
   * @param string|array $methods      A required HTTP method or an array of restricted methods
   * @param string       $condition    A condition that should evaluate to true for the route to match
   */
  public function routes() {
    $route_collection = new RouteCollection();

    foreach ($this->parDataManager->getParEntityTypes() as $definition) {
      $type = $definition->getBundleEntityType();
      $type_label = $definition->getBundleLabel();
      $bundles = $this->parDataManager->getBundles($definition);
      $id = $definition->id();
      $singular = $definition->getSingularLabel()->render();
      $plural = $definition->getPluralLabel()->render();

      // The routes for the PAR Entities themselves.
      // The canonical entity route.
      $route = new Route(
        '/admin/content/par_data/' . $id . '/{' . $id . '}',
        [
          '_entity_view' => $id,
          '_title' => $singular,
        ],
        [
          '_entity_access' => "{$id}.view",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.canonical", $route);

      // The collection route for viewing all of the entities.
      $route = new Route(
        "/admin/content/par_data/{$id}",
        [
          '_entity_list' => $id,
          '_title' => "Edit {$plural}",
        ],
        [
          '_permission' => "access {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.collection", $route);

      // The collection route for viewing all of the entities.
      $route = new Route(
        "/admin/content/par_data/{$id}/add",
        [
          '_controller' => DONT_KNOW_YET,
          '_title' => "Add {$plural}",
        ],
        [
          '_permission' => "add {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.collection", $route);

      // Only add routes for multiple bundles if there are multiple bundles.
      if (count($bundles) > 1) {
        $route = new Route(
          '/admin/content/par_data/' . $id . '/add/{' . $type . '}',
          [
            '_controller' => DONT_KNOW_YET,
            '_title' => "Add {$plural}",
          ],
          [
            '_permission' => "add {$id} entities",
          ],
          [
            '_admin_route' => TRUE,
          ]
        );
        $route_collection->add("entity.{$id}.collection", $route);
      }


//      entity.par_entity.edit_form:
//  path: '/admin/content/par_entity/{par_entity}/edit'
//  defaults:
//    _entity_form: par_entity.edit
//    _title: 'Edit par entity'
//  requirements:
//    _permission: 'edit par_entity entities'
//  options:
//    _admin_route: TRUE
//
//entity.par_entity.delete_form:
//  path: '/admin/content/par_entity/{par_entity}/delete'
//  defaults:
//    _entity_form: par_entity.delete
//    _title: 'Delete par entity'
//  requirements:
//    _permission: 'delete par_entity entities'
//  options:
//    _admin_route: TRUE
//
//par_data_entities.add_page:
//  path: '/admin/content/par_entity/add'
//  defaults:
//    _controller: '\Drupal\par_data_entities\Controller\ParEntityAddController::add'
//    _title: 'Add par entity'
//  requirements:
//    _permission: 'add par_entity entities'
//
//entity.par_entity.add_form:
//  path: '/admin/content/par_entity/add/{par_entity_type}'
//  defaults:
//    _controller: '\Drupal\par_data_entities\Controller\ParEntityAddController::addForm'
//    _title_callback: '\Drupal\par_data_entities\Controller\ParEntityAddController::getAddFormTitle'
//  options:
//    _admin_route: TRUE
//  requirements:
//    _permission: 'add par_entity entities'



      // The routes for the PAR Entity types.
      // The collection route for viewing all of the entities.
      $route = new Route(
        "/admin/content/par_data/{$type}",
        [
          '_entity_list' => $type,
          '_title' => "View all {$type_label} entities",
        ],
        [
          '_permission' => "administer site configuration",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.collection", $route);

      // The canonical entity route, used for editing.
      $route = new Route(
        '/admin/structure/par_data/' . $type . '/{' . $type . '}',
        [
          '_entity_form' => "{$type}.edit",
          '_title' => "Edit {$type_label}",
        ],
        [
          '_permission' => "administer {$type} entity types",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.canonical", $route);
      $route_collection->add("entity.{$type}.edit_form", $route);

      // The entity route for creating.
      $route = new Route(
        "/admin/structure/par_data/{$type}/add",
        [
          '_entity_form' => "{$type}.add",
          '_title' => "Add {$type_label}",
        ],
        [
          '_permission' => "administer {$type} entity types",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.add_form", $route);

      // The entity route for creating.
      $route = new Route(
        '/admin/structure/par_data/' . $type . '/{' . $type . '}/delete',
        [
          '_entity_form' => "{$type}.delete",
          '_title' => "Delete {$type_label}",
        ],
        [
          '_permission' => "administer {$type} entity types",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.delete_form", $route);

    }
      


    return $route_collection;
  }

}
