<?php

namespace Drupal\par_flows\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Link;
use Drupal\par_flows\ParRedirectTrait;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_flow",
 *   label = @Translation("PAR Form Flow"),
 *   config_prefix = "par_flow",
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\Core\Entity\EntityForm",
 *       "edit" = "Drupal\Core\Entity\EntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityConfirmFormBase"
 *     }
 *   },
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/par/flow/{par_entity_type}",
 *     "edit-form" = "/admin/config/par/flow/{par_entity_type}/edit",
 *     "delete-form" = "/admin/config/par/flow/{par_entity_type}/delete",
 *     "collection" = "/admin/config/par/flow"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "steps"
 *   }
 * )
 */
class ParFlow extends ConfigEntityBase {

  use ParRedirectTrait;

  /**
   * The flow ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The flow label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this flow.
   *
   * @var string
   */
  protected $description;

  /**
   * The steps for this flow.
   *
   * @var array
   */
  protected $steps;

  /**
   * Get the description for this flow.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Get all steps in this flow.
   */
  public function getSteps() {
    return $this->steps ?: [];
  }

  /**
   * Get a step by it's index.
   *
   * @param int $index
   *   The step number that is required.
   *
   * @return array
   *   An array with values for the form_id & route
   */
  public function getStep($index) {
    return isset($this->steps[$index]) ? $this->steps[$index] : NULL;
  }

  /**
   * Get a step by the form id.
   *
   * @param string $form_id
   *   The form id to lookup.
   *
   * @return array
   *   An array with values for the step, form_id & route
   */
  public function getStepByFormId($form_id) {
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['form_id']) && $form_id === $step['form_id']) {
        $match = [
          'step' => $key,
        ] + $step;
      }
    }
    return isset($match) ? $match : [];
  }

  /**
   * Get a step by the route.
   *
   * @param string $route
   *   The string representing a given route to lookup.
   *
   * @return array
   *   An array with values for the step, form_id & route
   */
  public function getStepByRoute($route) {
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['route']) && $route === $step['route']) {
        $match = [
            'step' => $key,
          ] + $step;
      }
    }
    return isset($match) ? $match : [];
  }

  /**
   * Get route for any given step.
   *
   * @param integer $index
   *   The step number to get a link for.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getRouteByStep($index, $link_options = []) {
    $step = $this->getStep($index);
    return isset($step['route']) ? $step['route'] : NULL;
  }

  /**
   * Get a form_id by the flow step.
   *
   * @param integer $index
   *   The step number to get a link for.
   *
   * @return array
   *   An array with values for the form_id & route
   */
  public function getFormIdByStep($index) {
    $step = $this->getStep($index);
    return isset($step['form_id']) ? $step['form_id'] : NULL;
  }

  /**
   * Get all the forms in a given flow.
   *
   * @return array
   *   An array of strings representing form IDs.
   */
  public function getFlowForms() {
    $forms = [];

    foreach ($this->getSteps() as $step) {
      if (isset($step['form_id'])) {
        $forms[] = (string) $step['form_id'];
      }
    }

    return $forms;
  }

  /**
   * Get link for any given step.
   *
   * @param integer $index
   *   The step number to get a link for.
   * @param array $route_params
   *   Additional route parameters to add to the route.
   * @param array $link_options
   *   An array of options to set on the link.
   *
   * @return Link
   *   A Drupal link object.
   */
  public function getLinkByStep($index, $route_params = [], $link_options = []) {
    $step = $this->getStep($index);
    $route = $step['route'];
    return $this->getLinkByRoute($route, $route_params, $link_options);
  }

}
