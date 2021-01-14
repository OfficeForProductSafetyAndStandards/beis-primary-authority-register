<?php

namespace Drupal\par_flows;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Defines the storage class for flows.
 *
 * This extends the base storage class adding extra
 * entity loading mechanisms.
 */
class ParFlowStorage extends ConfigEntityStorage {

  /**
   * Load Flows by route.
   *
   * @param string $route
   *   The route id to check for.
   *
   * @return array
   *   An array of flows keyed by id or an empty array if none were found.
   */
  public function loadByRoute($route) {
    $cache = \Drupal::cache('par_flows')->get("route:{$route}");
    if ($cache) {
      return $cache->data;
    }
    else {
      $flows = [];
      $tags = ['par_flows_route_flows'];
      foreach ($this->loadMultiple() as $flow) {
        if ($flow->getStepByRoute($route)) {
          $flows[$flow->id()] = $flow;
          $tags[] = "par_flow:{$flow->id()}";
        }
      }
      \Drupal::cache('par_flows')->set("route:{$route}", $flows, Cache::PERMANENT, $tags);
    }

    return $flows;
  }

  /**
   * Load Flows by form.
   *
   * @param string $form
   *   The form id to check for.
   *
   * @return array
   *   An array of flows keyed by id or an empty array if none were found.
   */
  public function loadByForm($form) {
    $cache = \Drupal::cache('par_flows')->get("form:{$form}");
    if ($cache) {
      return $cache->data;
    }
    else {
      $flows = [];
      $tags = ['par_flows_form_flows'];
      foreach ($this->loadMultiple() as $flow) {
        if ($flow->getStepByFormId($form)) {
          $flows[$flow->id()] = $flow;
          $tags[] = "par_flow:{$flow->id()}";
        }
      }
      \Drupal::cache('par_flows')->set("form:{$form}", $flows, Cache::PERMANENT, $tags);
    }

    return $flows;
  }
}
