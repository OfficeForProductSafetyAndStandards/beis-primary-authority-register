<?php

namespace Drupal\par_subscriptions\Permissions;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_subscriptions\ParSubscriptionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class ParSubscriptionPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The subscription manager.
   *
   * @var \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   */
  protected ParSubscriptionManagerInterface $subscriptionManager;

  /**
   * Constructs a subscription permissions.
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
   * Dynamic getter for the subscription manager.
   *
   * @return \Drupal\par_subscriptions\ParSubscriptionManagerInterface
   */
  private function getSubscriptionManager() {
    return $this->subscriptionManager ?? \Drupal::service('par_subscriptions.manager');
  }

  /**
   * Get permissions for Taxonomy Views Integrator.
   *
   * @return array
   *   Permissions array.
   */
  public function permissions() {
    $permissions = [];

    foreach ($this->getSubscriptionManager()->getLists() as $list) {
      // Subscribe to a list.
      $permissions += [
        "subscribe to $list" => [
          'title' => $this->t('Subscribe to subscription %list', ['%list' => $list]),
          'description' => $this->t('Allow users to subscribe to the %list subscription list.', ['%list' => $list]),
        ],
      ];

      // View a list.
      $permissions += [
        "view list $list" => [
          'title' => $this->t('View subscribers to %list', ['%list' => $list]),
          'description' => $this->t('Allow users to see who is subscribed to %list.', ['%list' => $list]),
        ],
      ];

      // Administer a list.
      $permissions += [
        "administer list $list" => [
          'title' => $this->t('Manage subscribers to %list', ['%list' => $list]),
          'description' => $this->t('Allow users to manage the %list subscription list.', ['%list' => $list]),
        ],
      ];
    }

    return $permissions;
  }

}
