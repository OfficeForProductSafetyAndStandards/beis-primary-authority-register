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
class ParFlow extends ConfigEntityBase implements ParFlowInterface {

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
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    return $this->steps ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStep($index) {
    return isset($this->steps[$index]) ? $this->steps[$index] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentStep() {
    // Lookup the current step to more accurately determine the next step.
    $current_step = $this->getStepByRoute($this->getCurrentRoute());
    return isset($current_step) ? $current_step : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getNextStep() {
    if ($current_step = $this->getCurrentStep()) {
      $next_index = ++$current_step;
    }

    $next_step = isset($next_index) ? $this->getStep($next_index) : $this->getStep(1);

    // If there is no next step we'll go back to the beginning.
    return isset($next_step['route']) ? $next_index : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrevStep() {
    if ($current_step = $this->getCurrentStep()) {
      $prev_index = --$current_step;
    }
    $prev_step = isset($prev_index) ? $this->getStep($prev_index) : $this->getStep(1);

    // If there is no next step we'll go back to the beginning.
    return isset($prev_step['route']) ? $prev_index : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getNextRoute() {
    return $this->getRouteByStep($this->getNextStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getPrevRoute() {
    return $this->getRouteByStep($this->getPrevStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getStepByFormId($form_id) {
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['form_id']) && $form_id === $step['form_id']) {
        $match = [
          'step' => $key,
        ] + $step;
      }
    }

    // If there is no next step we'll go back to the beginning.
    return isset($match['step']) ? $match['step'] : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getStepByRoute($route) {
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['route']) && $route === $step['route']) {
        $match = [
            'step' => $key,
          ] + $step;
      }
    }

    // If there is no next step we'll go back to the beginning.
    return isset($match['step']) ? $match['step'] : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteByStep($index) {
    $step = $this->getStep($index);
    return isset($step['route']) ? $step['route'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormIdByStep($index) {
    $step = $this->getStep($index);
    return isset($step['form_id']) ? $step['form_id'] : NULL;
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function getLinkByStep($index, array $route_params = [], array $link_options = []) {
    $step = $this->getStep($index);
    $route = $step['route'];
    return $this->getLinkByRoute($route, $route_params, $link_options);
  }

}
