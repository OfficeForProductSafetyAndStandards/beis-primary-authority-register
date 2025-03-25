<?php

namespace Drupal\par_manage_subscription_list_flows\Routing;

use Drupal\Component\Utility\Html;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionManageForm;
use Drupal\par_manage_subscription_list_flows\Form\ParSubscriptionReviewForm;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines dynamic routes.
 */
class ParSubscriptionManagementRoutes implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Constructs a ParDataPermissions instance.
   */
  public function __construct(
    protected ParSubscriptionManagerInterface $subscriptionManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function create(ContainerInterface $container) {
    return new static($container->get('par_subscriptions.manager'));
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   *   The PAR Subscription Manager service.
   */
  private function getSubscriptionManager() {
    return $this->subscriptionManager ?? \Drupal::service('par_subscriptions.manager');
  }

  /**
   * New Route()...
   */
  public function routes() {
    $route_collection = new RouteCollection();

    foreach ($this->getSubscriptionManager()->getLists() as $list) {
      $list_slug = Html::getClass($list);

      // Manage a list in bulk.
      $route = new Route(
        "/helpdesk/subscriptions/$list_slug/manage",
        [
          '_form' => ParSubscriptionManageForm::class,
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
        "/helpdesk/subscriptions/$list_slug/validate",
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
        "/helpdesk/subscriptions/$list_slug/review",
        [
          '_form' => ParSubscriptionReviewForm::class,
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
