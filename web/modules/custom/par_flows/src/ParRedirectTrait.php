<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityInterface;
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

    // All parameters must be sanitised.
    $route_params = [];
    foreach ($params as $key => $value) {
      // Note that the raw parameter cannot be set for arrays or any other non-scalar
      // values other due to lack of a transparent conversion method.
      if ($value instanceof EntityInterface) {
        $route_params[$key] = $value->id();
      }
      elseif (is_scalar($value)) {
        $route_params[$key] = $value;
      }
    }

    // Only add the route parameters required by the given route.
    foreach ($path_variables as $value) {
      if (!isset($route_params[$value])) {
        $route_params[$value] = \Drupal::service('par_flows.data_handler')->getRawParameter($value);
      }
    }

    return $route_params;
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
      'attributes' => ['class' => 'govuk-link']
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

}
