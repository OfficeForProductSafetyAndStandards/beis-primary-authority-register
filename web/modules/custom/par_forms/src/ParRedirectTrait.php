<?php

namespace Drupal\par_forms;

use Drupal\Core\Link;

trait ParRedirectTrait {

  /**
   * Get link for any given step.
   */
  public function getLinkByRoute($route, $link_options = []) {
    $route_params = $this->getRouteParams();
    $link_options += [
      'absolute' => TRUE,
      'attributes' => ['class' => 'flow-link']
    ];
    return Link::createFromRoute('', $route, $route_params, $link_options);
  }

  /**
   * Get the params for a dynamic route.
   */
  public function getRouteParams() {
    // Submit the route with all the same parameters.
    return $route_params = \Drupal::routeMatch()->getRawParameters()->all();
  }

}
