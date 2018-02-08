<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;

trait ParRedirectTrait {

  /**
   * Get link for any given step.
   */
  public function getLinkByRoute($route, $route_params = [], $link_options = []) {
    $route_provider = \Drupal::service('router.route_provider');
    $path_variables = $route_provider->getRouteByName($route)->compile()->getPathVariables();

    // Automatically add the route params from the current route if needed.
    foreach ($this->getRouteParams() as $current_route_param => $value) {
      if (in_array($current_route_param, $path_variables)) {
        $route_params[$current_route_param] = $value;
      }
    }

    $link_options += [
      'absolute' => TRUE,
//      'attributes' => ['class' => 'flow-link']
    ];
    return Link::createFromRoute('', $route, $route_params, $link_options);
  }

  /**
   * Get the current route.
   */
  public function getCurrentRoute() {
    // Submit the route with all the same parameters.
    return $route_params = \Drupal::routeMatch()->getRouteName();
  }

  /**
   * Get the params for a dynamic route.
   */
  public function getRouteParams() {
    // Submit the route with all the same parameters.
    return $route_params = \Drupal::routeMatch()->getRawParameters()->all();
  }

  /**
   * Get a specific route parameter.
   */
  public function getRouteParam($key) {
    return $route_params = \Drupal::routeMatch()->getParameter($key);
  }



}
