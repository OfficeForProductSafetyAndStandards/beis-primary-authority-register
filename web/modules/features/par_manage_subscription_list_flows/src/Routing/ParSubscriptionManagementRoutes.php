<?php

namespace Drupal\par_manage_subscription_list_flows\Routing;

use Drupal\Component\Utility\Html;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic routes.
 */
class ParSubscriptionManagementRoutes implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Constructs a ParDataPermissions instance.
   */
  public function __construct(ParSubscriptionManagerInterface $par_subscriptions_manager) {
    $this->subscriptionManager = $par_subscriptions_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_subscriptions.manager'));
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   */
  private function getSubscriptionManager() {
    return $this->subscriptionManager ?? \Drupal::service('par_subscriptions.manager');
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

    foreach($this->getSubscriptionManager()->getLists() as $list) {
      $list_slug = Html::getClass($list);

      // Manage a list in bulk.
      $route = new Route(
        "/subscriptions/$list_slug/manage",
        [
          '_form' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionManageForm',
          '_title_callback' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionManageForm::titleCallback',
          'list' => $list,
        ],
        [
          '_permission' => "administer list $list",
        ]
      );
      $route_collection->add("par_manage_subscription_list_flows.{$list}.manage", $route);

      // Review changes to a list.
      $route = new Route(
        "/subscriptions/$list_slug/validate",
        [
          '_form' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionValidateForm',
          '_title_callback' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionValidateForm::titleCallback',
          'list' => $list,
        ],
        [
          '_permission' => "administer list $list",
        ]
      );
      $route_collection->add("par_manage_subscription_list_flows.{$list}.validate", $route);

      // Review changes to a list.
      $route = new Route(
        "/subscriptions/$list_slug/review",
        [
          '_form' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionReviewForm',
          '_title_callback' => 'Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionReviewForm::titleCallback',
          'list' => $list,
        ],
        [
          '_permission' => "administer list $list",
        ]
      );
      $route_collection->add("par_manage_subscription_list_flows.{$list}.review", $route);
    }

    return $route_collection;
  }

}
