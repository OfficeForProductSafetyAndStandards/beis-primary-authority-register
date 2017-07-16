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
      $name = $definition->getLabel();

      // The canonical entity route.
      $route = new Route(
        '/admin/content/par_data/' . $type . '/{' . $type . '}',
        [
          '_entity_view' => $type,
          '_title' => $name,
        ],
        [
          '_entity_access' => "{$type}.view",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.canonical", $route);

      // The collection route for viewing all of the entities.
      $route = new Route(
        '/admin/content/par_data/' . $type,
        [
          '_entity_list' => $type,
          '_title' => "View all {$name} entities",
        ],
        [
          '_permission' => "access {$type} entities",
        ],
        [
          '_admin_route' => TRUE,
        ]
      );
      $route_collection->add("entity.{$type}.collection", $route);
    }
      
      

    return $route_collection;
  }

}
