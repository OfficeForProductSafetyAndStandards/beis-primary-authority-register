<?php

namespace Drupal\par_login\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class ParUserRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Require higher privileges to view the user profile page.
    if ($route = $collection->get('entity.user.canonical')) {
      $route->setRequirement('_permission', 'administer users');
    }
  }
}
