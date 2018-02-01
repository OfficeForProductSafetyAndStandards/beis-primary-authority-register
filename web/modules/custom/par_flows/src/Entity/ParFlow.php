<?php

namespace Drupal\par_flows\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_flows\ParDefaultActionsTrait;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_flow",
 *   label = @Translation("PAR Form Flow"),
 *   config_prefix = "par_flow",
 *   handlers = {
 *     "storage" = "Drupal\par_flows\ParFlowStorage",
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
 *     "canonical" = "/admin/config/par/flows/{par_entity_type}",
 *     "edit-form" = "/admin/config/par/flows/{par_entity_type}/edit",
 *     "delete-form" = "/admin/config/par/flows/{par_entity_type}/delete",
 *     "collection" = "/admin/config/par/flows"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "default_title",
 *     "default_section_title",
 *     "description",
 *     "save_method",
 *     "steps"
 *   }
 * )
 */
class ParFlow extends ConfigEntityBase implements ParFlowInterface {

  use StringTranslationTrait;
  use ParRedirectTrait;
  use ParDefaultActionsTrait;

  const SAVE_STEP = 'step';
  const SAVE_END = 'end';

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
   * The default page title for the flow.
   *
   * @var string
   */
  protected $default_title;

  /**
   * The default subheader title for the flow.
   *
   * @var string
   */
  protected $default_section_title;

  /**
   * A brief description of this flow.
   *
   * @var string
   */
  protected $description;

  /**
   * The method for saving flow data.
   *
   * Allowed values: 'end' (default), 'step'
   *
   * Typically for GDS flows this will be at the end,
   * however, this can be configured to save on each step
   * by setting the value to 'step'
   *
   * These are the defaults and can be overridden on a
   * form by form basis.
   *
   * @var string
   */
  protected $save_method;

  /**
   * The route parameters by which this flow can vary.
   *
   * Typically used to distinguish individual instances of
   * flows such as when completing this flow with different data.
   *
   * The state should be the same for all steps in this flow.
   *
   * e.g. When updating a partnership the data should be stored
   * in a separate temporary storage cache to the data entered
   * when updating other partnerships.
   *
   * @var array
   */
  protected $states = [];

  /**
   * The steps for this flow.
   *
   * @var array
   */
  protected $steps;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);

    // Set the default actions depending on flow save method.
    $default_actions = $this->getSaveMethod() === ParFlow::SAVE_STEP ? ['save', 'cancel'] : ['next', 'cancel'];
    $this->setDefaultActions($default_actions);

    // Then let's go back and set any additional operations.
    $actions = $this->getCurrentStepOperations();
    $this->setActions($actions);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultTitle() {
    return $this->default_title;
  }

  /**
   * Get the default section title to show in GDS subheader.
   */
  public function getDefaultSectionTitle() {
    return $this->default_section_title;
  }

  /**
   * {@inheritdoc}
   */
  public function getSaveMethod() {
    return $this->save_method === self::SAVE_STEP ? self::SAVE_STEP : self::SAVE_END;
  }

  /**
   * {@inheritdoc}
   */
  public function getStates() {
    return !empty($this->states) ? $this->states : [];
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
  public function getNextStep($operation = NULL) {
    $current_step = $this->getCurrentStep();
    $redirect = $this->getStepByOperation($current_step, $operation);

    // First check if the operation produced a valid step.
    if ($redirect) {
      $next_step = isset($redirect) && $this->getStep($redirect) ? $this->getStep($redirect) : NULL;
      $next_index = $redirect;
    }

    // Then fallback to the next step, or the first if already on the last.
    if (!isset($next_step) && $current_step === count($this->getSteps())) {
      $next_step = $this->getStep(1);
      $next_index = 1;
    }
    else if (!isset($next_step) && $current_step) {
      $next_index = ++$current_step;
      $next_step = isset($next_index) ? $this->getStep($next_index) : NULL;
    }

    // If there is no next step we'll go back to the beginning.
    $step = isset($next_index) && isset($next_step['route']) ? $next_index : NULL;

    if (empty($step)) {
      throw new ParFlowException('The specified route does not exist.');
    }

    return $step;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrevStep($operation = NULL) {
    $current_step = $this->getCurrentStep();
    $redirect = $this->getStepByOperation($current_step, $operation);

    // First check if the operation produced a valid step.
    if ($redirect) {
      $prev_step = isset($redirect) && $this->getStep($redirect) ? $this->getStep($redirect) : NULL;
      $prev_index = $redirect;
    }

    // Then fallback to the next step, or the first if already on the last.
    if (!isset($prev_step) && $current_step === 1) {
      $prev_step = $this->getStep(1);
      $prev_index = 1;
    }
    else if (!isset($prev_step)){
      $prev_index = --$current_step;
      $prev_step = isset($prev_index) ? $this->getStep($prev_index) : $this->getStep(1);
    }

    // If there is no next step we'll go back to the beginning.
    $step = isset($prev_step) && isset($prev_step['route']) ? $prev_index : NULL;

    if (empty($step)) {
      throw new ParFlowException('The specified route does not exist.');
    }

    return $step;
  }

  /**
   * {@inheritdoc}
   */
  public function getNextRoute($operation = NULL) {
    return $this->getRouteByStep($this->getNextStep($operation));
  }

  /**
   * {@inheritdoc}
   */
  public function getPrevRoute($operation = NULL) {
    return $this->getRouteByStep($this->getPrevStep($operation));
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentStepComponents() {
    return $this->getStepComponents($this->getCurrentStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentStepFormDataKeys() {
    return $this->getStepFormDataKeys($this->getCurrentStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentStepOperations() {
    return $this->getStepOperations($this->getCurrentStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getStepComponents($index) {
    $step = $this->getStep($index);
    return isset($step['components']) ? $step['components'] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStepFormDataKeys($index) {
    $step = $this->getStep($index);
    return isset($step['form_data']) ? $step['form_data'] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStepOperations($index) {
    $step = $this->getStep($index);
    $redirects = isset($step['redirect']) ? $step['redirect'] : [];

    // Get the default actions, these can be disabled
    // if required on a form by form basis.
    // See ParDefaultActionsTrait::disableAction()
    $defaults = $this->getActions();

    // Get the values for the given step.
    $step_values = !empty($redirects) ? array_keys($redirects) : [];

    if (!empty($defaults) && !empty($step_values)) {
      return array_unique(array_merge($defaults, $step_values));
    }
    elseif (!empty($step_values)) {
      return $step_values;
    }
    else {
      return $defaults;
    }
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

    // If there is no step we'll go back to the beginning.
    return isset($match['step']) ? $match['step'] : NULL;
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

    // If there is no step we'll go back to the beginning.
    return isset($match['step']) ? $match['step'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStepByOperation($index, $operation) {
    $step = $this->getStep($index);
    $redirects = isset($step['redirect']) ? $step['redirect'] : [];

    // If there is no matching step then we'll just return the original step.
    return isset($redirects[$operation]) ? $redirects[$operation] : NULL;
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
    if (empty($step)) {
      throw new ParFlowException('The specified route does not exist.');
    }
    return $route ? $this->getLinkByRoute($route, $route_params, $link_options) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  protected function getLinkByOperation($index, $operation, array $route_params = [], array $link_options = []) {
    $step = $this->getStepByOperation($index, $operation);
    return $this->getLinkByStep($step, $route_params, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getLinkByCurrentOperation($operation, array $route_params = [], array $link_options = []) {
    return $this->getLinkByOperation($this->getCurrentStep(), $operation, $route_params, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getNextLink($operation = NULL, array $route_params = [], array $link_options = []) {
    $step = $this->getNextStep($operation);
    return $this->getLinkByStep($step, $route_params, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getPrevLink($operation = NULL, array $route_params = [], array $link_options = []) {
    $step = $this->getPrevStep($operation);
    return $this->getLinkByStep($step, $route_params, $link_options);
  }
}
