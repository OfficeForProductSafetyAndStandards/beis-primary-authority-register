<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Url;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

trait ParRedirectTrait {

  /**
   * Get link for any given step.
   */
  public function getLinkByRoute($route, $route_params = [], $link_options = [], $check_access = FALSE) {
    $route_provider = \Drupal::service('router.route_provider');
    try {
      $path_variables = $route_provider->getRouteByName($route)
        ->compile()
        ->getPathVariables();
    }
    catch (RouteNotFoundException $e) {
      throw new ParFlowException(t('This flow cannot find the route @route', ['@route' => $route]));
    }

    // Automatically add the route params from the current route if needed.
    foreach ($this->getRouteParams() as $current_route_param => $value) {
      if (in_array($current_route_param, $path_variables) && !isset($route_params[$current_route_param])) {
        $route_params[$current_route_param] = $value;
      }
    }

    $link_options += [
      'absolute' => TRUE,
      'attributes' => ['class' => 'flow-link']
    ];

    $url = Url::fromRoute($route, $route_params, $link_options);
    $link = Link::fromTextAndUrl('', $url);

    return !$check_access || ($url->access() && $url->isRouted()) ? $link : NULL;
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

  /**
   * PAR specific redirection.
   */
  public function parRedirect($route_name, array $route_parameters = [], array $options = [], $status = 302) {
    // Determine whether to use the 'destination' query parameter
    // to determine redirection preferences.
    $options = [];
    $query = $this->getRequest()->query;
    if ($this->skipQueryRedirection && $query->has('destination')) {
      $options['query']['destination'] = $query->get('destination');
      $query->remove('destination');
    }

    return $this->redirect($route_name, $route_parameters, $options, $status);
  }

}
