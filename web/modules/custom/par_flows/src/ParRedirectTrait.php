<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Url;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

trait ParRedirectTrait {

  /**
   * Get the parameters required for a given route.
   *
   * @param $route
   *   The route name.
   * @param $params
   *   A bag of parameters to choose from.
   *
   * @return array
   *   The bag of sorted route parameters.
   */
  public function getRequiredParams($route, $params = []) {
    $route_provider = \Drupal::service('router.route_provider');
    try {
      $path_variables = $route_provider->getRouteByName($route)
        ->compile()
        ->getPathVariables();
    }
    catch (RouteNotFoundException $e) {
      throw new ParFlowException(t('This flow cannot find the route @route', ['@route' => $route]));
    }
    catch (MissingMandatoryParametersException $e) {
      throw new ParFlowException(t('The parameters are missing for the route @route', ['@route' => $route]));
    }

    // Only add the route parameters required by the given route.
    foreach ($path_variables as $value) {
      if (!isset($params[$value])) {
        $params[$value] = \Drupal::service('par_flows.data_handler')->getRawParameter($value);
      }
    }

    return $params;
  }

  /**
   * Get link for any given step.
   */
  public function getLinkByRoute($route, $route_params = [], $link_options = [], $check_access = FALSE) {
    $params = $this->getRequiredParams($route, $route_params);
    $url = Url::fromRoute($route, $params);

    return $this->getLinkByUrl($url, '', $link_options);
  }

  /**
   * Get link for any given step.
   */
  public function getLinkByUrl(Url $url, $text = '', $link_options = []) {
    $link_options += [
      'absolute' => TRUE,
      'attributes' => ['class' => 'flow-link']
    ];

    $url->mergeOptions($link_options);
    $link = Link::fromTextAndUrl($text, $url);

    return ($url->access() && $url->isRouted()) ? $link : NULL;
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
    return \Drupal::service('par_flows.data_handler')->getRawParameters();
  }

  /**
   * Get a specific route parameter.
   */
  public function getRouteParam($key) {
    return \Drupal::service('par_flows.data_handler')->getParameter($key);
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
