<?php

namespace Drupal\par_data\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic routes.
 */
class ParDataRoutes implements ContainerInjectionInterface {

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
          '_title' => $plural,
        ],
        [
          '_permission' => "access {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.collection", $route);

      // The main add page for the entity.
      $route = new Route(
        "/admin/content/par_data/{$id}/add",
        [
          '_controller' => 'Drupal\par_data\Controller\ParDataAddController::add',
          '_title' => "Add {$singular}",
          'par_data_entity' => $id,
        ],
        [
          '_permission' => "add {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("par_data.{$id}.add_page", $route);

      // The create route for specific bundles.
      $route = new Route(
        '/admin/content/par_data/' . $id . '/add/{par_data_entity_type}',
        [
          '_controller' => 'Drupal\par_data\Controller\ParDataAddController::addForm',
          '_title' => "Add {$singular}",
          'par_data_entity' => $id,
        ],
        [
          '_permission' => "add {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("par_data.{$id}.add_form", $route);

      // The main edit page for the entity.
      $route = new Route(
        '/admin/content/par_data/' . $id . '/{' . $id . '}/edit',
        [
          '_entity_form' => "{$id}.edit",
          '_title' => "Edit {$singular}",
        ],
        [
          '_permission' => "edit {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.edit_form", $route);

      // The main delete page for the entity.
      $route = new Route(
        '/admin/content/par_data/' . $id . '/{' . $id . '}/delete',
        [
          '_entity_form' => "{$id}.delete",
          '_title' => "Add {$singular}",
        ],
        [
          '_permission' => "delete {$id} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$id}.delete_form", $route);


      // The routes for the PAR Entity types.
      // The collection route for viewing all of the entities.
      $route = new Route(
        "/admin/structure/par_data/{$type}",
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
