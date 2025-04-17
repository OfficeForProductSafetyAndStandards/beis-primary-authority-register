<?php

namespace Drupal\par_subscriptions\Routing;

use Drupal\par_subscriptions\Form\ParSubscribeForm;
use Drupal\par_subscriptions\Form\ParVerifyForm;
use Drupal\par_subscriptions\Form\ParUnsubscribeForm;
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
class ParSubscriptionRoutes implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The subscription manager service.
   *
   * @var \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   */
  protected $subscriptionManager;

  /**
   * Constructs a ParDataPermissions instance.
   */
  public function __construct(ParSubscriptionManagerInterface $par_subscriptions_manager) {
    $this->subscriptionManager = $par_subscriptions_manager;
  }

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

      // Subscribe to a list.
      $route = new Route(
        "/subscription-list/$list_slug/subscribe/{subscription_status}",
        [
          '_form' => ParSubscribeForm::class,
          '_title_callback' => 'Drupal\par_subscriptions\Form\ParSubscribeForm::titleCallback',
          'list' => $list,
          'subscription_status' => NULL,
        ],
        [
          '_permission' => "subscribe to $list",
        ]
      );
      $route_collection->add("par_subscriptions.{$list}.subscribe", $route);

      // Verify subscription to a list.
      $route = new Route(
        "/subscription-list/$list_slug/verify/{subscription_code}",
        [
          '_form' => ParVerifyForm::class,
          '_title_callback' => 'Drupal\par_subscriptions\Form\ParVerifyForm::titleCallback',
          'list' => $list,
        ],
        [
          '_permission' => "subscribe to $list",
        ]
      );
      $route_collection->add("par_subscriptions.{$list}.verify", $route);

      // Unsubscribe from a list.
      $route = new Route(
        "/subscription-list/$list_slug/unsubscribe/{subscription_code}",
        [
          '_form' => ParUnsubscribeForm::class,
          '_title_callback' => 'Drupal\par_subscriptions\Form\ParUnsubscribeForm::titleCallback',
          'list' => $list,
          'subscription_code' => NULL,
        ],
        [
          '_permission' => "subscribe to $list",
        ]
      );
      $route_collection->add("par_subscriptions.{$list}.unsubscribe", $route);
    }

    return $route_collection;
  }

}
