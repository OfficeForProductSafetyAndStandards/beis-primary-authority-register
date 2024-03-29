<?php

namespace Drupal\par_flows\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParDefaultActionsTrait;
use Drupal\par_flows\ParFlowDataHandler;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\par_forms\ParFormPluginInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_flow",
 *   label = @Translation("PAR Form Flow"),
 *   config_prefix = "par_flow",
 *   handlers = {
 *     "storage" = "Drupal\par_flows\ParFlowStorage",
 *     "list_builder" = "Drupal\par_flows\FlowListBuilder",
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
 *     "canonical" = "/admin/config/flows/{par_entity_type}",
 *     "edit-form" = "/admin/config/flows/{par_entity_type}/edit",
 *     "delete-form" = "/admin/config/flows/{par_entity_type}/delete",
 *     "collection" = "/admin/config/flows"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "default_title",
 *     "default_section_title",
 *     "description",
 *     "save_method",
 *     "states",
 *     "final_routes",
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
  const BACK_STEP = 'back';
  const CANCEL_STEP = 'cancel';
  const DONE_STEP = 'done';

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
   * The exit routes to return to if the journey completes.
   *
   * @var array
   */
  protected $final_routes = [];

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
  protected $states;

  /**
   * The steps for this flow.
   *
   * @var array
   */
  protected $steps;

  /**
   * The current route used to determine which part of the flow is being dealt with.
   *
   * @var RouteMatchInterface
   */
  protected $currentRoute;

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
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  public function getEventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

  /**
   * Get the router service.
   *
   * @return \Drupal\Core\Routing\AccessAwareRouterInterface
   */
  public function getRouter() {
    return \Drupal::service('router');
  }

  /**
   * Get the current route.
   *
   * @return RouteMatchInterface
   */
  public function getCurrentRouteMatch() {
    // Submit the route with all the same parameters.
    return $this->currentRoute ?? \Drupal::service('par_flows.negotiator')
      ->getRoute();
  }

  /**
   * Get the current route.
   */
  public function setCurrentRouteMatch($route) {
    // Store the current route.
    $this->currentRoute = $route;
  }

  /**
   * Get the current route name.
   */
  public function getCurrentRoute() {
    // Submit the route with all the same parameters.
    return $route_params = $this->getCurrentRouteMatch()->getRouteName();
  }

  /**
   * Get the params for a dynamic route.
   */
  public function getRouteParams() {
    // Submit the route with all the same parameters.
    return $this->getCurrentRouteMatch()->getRawParameters()->all();
  }

  /**
   * Get a specific route parameter.
   */
  public function getRouteParam($key) {
    return $this->getCurrentRouteMatch()->getParameter($key);
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
  public function getFinalRoutes() {
    $routes = [];
    foreach ($this->final_routes as $final_route) {
      try {
        // Check that the route exists before accepting it.
        $route_provider = \Drupal::service('router.route_provider');
        if ($route_provider->getRouteByName($final_route)) {
          $routes[] = $final_route;
        }
      }
      catch (RouteNotFoundException $e) {

      }
    }

    return $routes;
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
    return $this->steps[$index] ?? NULL;
  }

  /**
   * A static helper to get the plugin name based on the configuration options.
   *
   * @param $key
   * @param $settings
   *
   * @return string
   *   The plugin name.
   */
  static function getComponentName($key, $settings) {
    return $settings[ParFormPluginInterface::NAME_PROPERTY] ?? $key;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentStep() {
    // Lookup the current step to more accurately determine the next step.
    $current_step = $this->getStepByRoute($this->getCurrentRoute());
    return $current_step ?? NULL;
  }

  /**
   * Start the journey.
   *
   * @return Url
   */
  public function start($step = 1, $params = []): Url {
    $route = $this->getRouteByStep($step);

    if (!isset($route)) {
      throw new ParFlowException('The start page for this journey could not be located');
    }

    $route_params = $this->getRequiredParams($route, $params);
    $url = Url::fromRoute($route, $route_params);

    $this->mergeOptions($url);
    return $url;
  }

  /**
   * Provide the url for a given step within the flow.
   *
   * @param string $operation
   *   The operation being performed, mandatory.
   * @param array $params
   *   Additional params to be used for determining the route.
   *
   * @throws ParFlowException
   *
   * @return ?Url
   */
  public function goto($operation, $params = []): ?Url {
    // Don't process if no operation has been set.
    if (NULL === $operation) {
      throw new ParFlowException('No operation was provided to redirect to.');
    }

    $step = $this->getCurrentStep();

    // The operation must return a valid route.
    $redirect_step = $this->getStepByOperation($step, $operation);
    $route = $redirect_step ? $this->getRouteByStep($redirect_step) : NULL;

    // If no route is found for the operation throw an error.
    if (!$route) {
      throw new ParFlowException('No route could be found for the given operation.');
    }

    $route_params = $this->getRequiredParams($route, $params);
    $url = Url::fromRoute($route, $route_params);

    $this->mergeOptions($url);
    return $url;
  }


  /**
   * {@inheritdoc}
   */
  public function progress($operation = NULL, $params = []): Url {
    // Run the event dispatcher to determine the order of precedence to determine the next route.
    $event = new ParFlowEvent($this, $this->getCurrentRouteMatch(), $operation, $params);
    $this->getEventDispatcher()->dispatch($event, ParFlowEvents::getEventByAction($operation));

    if (!$event->getUrl() instanceof Url) {
      throw new ParFlowException('Could not find an appropriate page to progress to.');
    }

    $url = $event->getUrl();

    $this->mergeOptions($url);
    return $url;
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
    return $step['components'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStepFormDataKeys($index) {
    $step = $this->getStep($index);
    return $step['form_data'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getStepFormDataKey($index, $key) {
    return $this->getStepFormDataKeys($index)[$key] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStepOperations($index) {
    $step = $this->getStep($index);
    $redirects = $step['redirect'] ?? [];

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
  public function getStepByFormId($form_key) {
    // Look up the form id from the form data config, the form data keys map to form IDs
    // and provide a consistent way for components to refer to similar forms across different journeys.
    $form_data_step = $this->getStepFormDataKey($this->getCurrentStep(), $form_key);

    // If the form data keys contained a reference to a form id then use this, otherwise
    // use the form key directly to look up the step.
    $form_id = $form_data_step ?? $form_key;

    // Look through all steps in the journey to find a form id that matches.
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['form_id']) && $form_id === $step['form_id']) {
        return $key;
      }
    }

    // If no step can be found that represents this form id.
    return NULL;
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

    return $match['step'] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStepByFormDataKey($form_data_key, $step = NULL) {
    $form_data_keys = $this->getStepFormDataKeys($step);
    return isset($form_data_keys[$form_data_key]) ? $this->getStepByFormId($form_data_keys[$form_data_key]) : NULL;
  }


  /**
   * {@inheritdoc}
   */
  public function getStepByOperation($index, $operation) {
    $step = $this->getStep($index);
    $redirects = $step['redirect'] ?? [];

    return $redirects[$operation] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteByStep($index) {
    $step = $this->getStep($index);
    return $step['route'] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormIdByStep($index) {
    $step = $this->getStep($index);
    return $step['form_id'] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormIdByCurrentStep() {
    return $this->getFormIdByStep($this->getCurrentStep());
  }

  /**
   * {@inheritdoc}
   */
  public function getStepByCurrentFormDataKey($form_data_key) {
    return $this->getStepByFormDataKey($form_data_key, $this->getCurrentStep());
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
   *
   * @deprecated Use ParFlow::getStartLink() instead.
   */
  public function getLinkByStep($index, array $route_params = [], array $link_options = [], $check_access = FALSE) {
    $step = $this->getStep($index);
    if (empty($step)) {
      throw new ParFlowException("The specified route does not exist for step {$index}.");
    }

    $route = $step['route'];

    /** @var Link $link */
    $link = $this->getLinkByRoute($route, $route_params, $link_options, $check_access);

    return $link ? $link : NULL;
  }

  /**
   * {@inheritdoc}
   *
   * @deprecated
   */
  public function getLinkByCurrentOperation($operation, array $route_params = [], array $link_options = [], $check_access = FALSE) {
    return $this->getOperationLink($operation, '', $route_params, $link_options);
  }

  /**
   * {@inheritdoc}
   *
   * @deprecated Use ParFlow::getFlowLink() instead.
   */
  public function getNextLink($operation = NULL, array $route_params = [], array $link_options = [], $check_access = FALSE) {
    return $this->getFlowLink($operation, '', $route_params, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getStartLink($index = 1, $text = '', array $params = [], array $link_options = []) {
    // Get a link specific to the given operation.
    $url = $this->start($index, $params);

    return $this->getLinkByUrl($url, $text, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperationLink($operation, $text = '', array $params = [], array $link_options = []) {
    // Get a link specific to the given operation.
    $url = $this->goto($operation, $params);

    return $this->getLinkByUrl($url, $text, $link_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getFlowLink($operation, $text = '', array $params = [], array $link_options = []) {
    // Get the next best link.
    $url = $this->progress($operation, $params);

    return $this->getLinkByUrl($url, $text, $link_options);
  }
}
