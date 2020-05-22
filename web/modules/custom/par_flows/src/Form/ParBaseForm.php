<?php

namespace Drupal\par_flows\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowDataHandler;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_forms\ParEntityValidationMappingTrait;
use Drupal\par_forms\ParFormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParBaseForm extends FormBase implements ParBaseInterface {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use StringTranslationTrait;
  use ParControllerTrait {
    ParControllerTrait::getParDataManager insteadof ParEntityValidationMappingTrait;
  }
  use ParEntityValidationMappingTrait;

  /**
   * The access result
   *
   * @var \Drupal\Core\Access\AccessResult
   */
  protected $accessResult;

  /**
   * Whether to skip redirection based on the 'destination' query parameter.
   *
   * This is typically done if we want to group two sets of forms together,
   * in which case we ignore the destination parameter for this form but
   * pass it on to the next route. Once the next form is completed it will be
   * redirected to the destination parameter.
   *
   * @var boolean
   */
  protected $skipQueryRedirection = FALSE;

  /**
   * Keys to be ignored for the saved data.
   *
   * Example: ['save', 'next', 'cancel'].
   *
   * @var array
   */
  protected $ignoreValues = ['save', 'done', 'next', 'cancel'];

  /**
   * List the mapping between the entity field and the form field.
   *
   * Array of entities to be used to validation. Each entity will have an array
   * of entity field name and form field name.
   *
   * Example: [
   *   'par_data_person:person' => [
   *     'first_name' => 'first_name',
   *     'last_name' => 'last_name',
   *     'work_phone' => 'phone',
   *   ],
   * ]
   *
   * @var array
   */
  protected $formItems = [];

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $negotiation
   *   The flow negotiator.
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface $data_handler
   *   The flow data handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   *   The par form builder.
   */
  public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager) {
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
    $this->parDataManager = $par_data_manager;
    $this->formBuilder = $plugin_manager;

    $this->setCurrentUser();

    // @TODO Move this to middleware to stop it being loaded when this controller
    // is contructed outside a request for a route this controller resolves.
    try {
      $this->getFlowNegotiator()->getFlow();

      $this->loadData();
    } catch (ParFlowException $e) {

    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('par_flows.negotiator'),
      $container->get('par_flows.data_handler'),
      $container->get('par_data.manager'),
      $container->get('plugin.manager.par_form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.roles', 'route'];
  }

  /**
   * Get ignored form values.
   *
   * @return array
   *   An array representing additional key names to be removed from form data.
   */
  public function getIgnoredValues() {
    return isset($this->ignoreValues) ? (array) $this->ignoreValues : [];
  }

  /**
   * Get list of mapped fields.
   *
   * @return array
   *   Array of entities and their mapped fields.
   */
  public function getFormItems() {
    return $this->formItems;
  }

  /**
   * Set ignored form values.
   *
   * @param array $values
   *   Configure additional key names to be removed from form data.
   */
  public function setIgnoredValues(array $values) {
    if (isset($values)) {
      $this->ignoreValues = $values;
    }
  }

  /**
   * Access callback
   * Useful for custom business logic for access.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account being checked.
   *
   * @see \Drupal\Core\Access\AccessResult
   *   The options for callback.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    return $this->accessResult ? $this->accessResult : AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    if ($form_id = $this->getFlowNegotiator()->getFlow()->getFormIdByCurrentStep()) {
      return $form_id;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->initializeFlow();

    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      // If there's is a cardinality parameter present display only this item.
      $cardinality = $this->getFlowDataHandler()->getParameter('cardinality');
      $index = isset($cardinality) ? (int) $cardinality : NULL;

      // Handle instances where FormBuilderInterface should return a redirect response.

      $plugin = $this->getFormBuilder()->getPluginElements($component, $form, $index);
      if ($plugin instanceof RedirectResponse) {
        return $plugin;
      }
    }

    // The components have weights around the 100 mark,
    // so the actions must always come last.
    $form['actions']['#weight'] = 999;

    // Only ever place a 'done' action by itself.
    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $form['actions']['done'] = [
        '#type' => 'submit',
        '#name' => 'done',
        '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Done'),
        '#limit_validation_errors' => [],
        '#attributes' => [
          'class' => ['cta-submit']
        ],
      ];
    }
    else {
      // Only ever do one of either 'next', 'save', 'upload'.
      if ($this->getFlowNegotiator()->getFlow()->hasAction('upload')) {
        $form['actions']['upload'] = [
          '#type' => 'submit',
          '#name' => 'upload',
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Upload'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('save')) {
        $form['actions']['save'] = [
          '#type' => 'submit',
          '#name' => 'save',
          '#submit' => ['::submitForm', '::saveForm'],
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Save'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $form['actions']['next'] = [
          '#type' => 'submit',
          '#name' => 'next',
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Continue'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }

      if ($this->getFlowNegotiator()->getFlow()->hasAction('cancel')) {
        $form['actions']['cancel'] = [
          '#type' => 'submit',
          '#name' => 'cancel',
          '#value' => $this->t('Cancel'),
          '#submit' => ['::cancelForm'],
          '#limit_validation_errors' => [],
          '#attributes' => [
            'class' => ['btn-link']
          ],
        ];
      }
    }

    $cache = [
      '#cache' => [
        'contexts' => $this->getCacheContexts(),
        'tags' => $this->getCacheTags(),
        'max-age' => $this->getCacheMaxAge(),
      ],
    ];

    return $form + $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);

    // We don't want to validate if just removing items.
    $remove_action = strpos($form_state->getTriggeringElement()['#name'], 'remove:');
    if ($remove_action !== FALSE) {
      return;
    }

    // If there's is a cardinality parameter present display only this item.
    // @TODO Consider re-using this pattern, but not needed now.
    $cardinality = $this->getFlowDataHandler()->getParameter('cardinality');

    // Validate all the plugins first.
    foreach ($this->getComponents() as $component) {
      $this->getFormBuilder()->validatePluginElements($component, $form, $form_state, $cardinality);
    }

    // Validate all the form elements.
    foreach ($this->createMappedEntities() as $entity) {
      $values = $form_state->getValues();
      $this->buildEntity($entity, $values);

      // Validate the built entities by field only.
      $violations = [];
      try {
        $field_names = $this->getFieldNamesByEntityType($entity->getEntityTypeId());
        $violations = $entity->validate()->filterByFieldAccess()->getByFields($field_names);
      }
      catch(\Exception $e) {
        $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %entity_id: @details.', ['%entity_id' => $entity->getEntityTypeId(), '@details' => $e->getMessage()]);
      }

      // For each violation set the correct error message.
      foreach ($violations as $violation) {
        if ($mapping = $this->getElementByViolation($violation)) {
          $element = $this->getElementKey($mapping->getElement());
          $name = $this->getElementName($element);
          $id = $this->getElementId($element, $form);

          $message = $this->getViolationMessage($violation, $mapping, $id);
          $form_state->setErrorByName($name, $message);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);

    try {
      $entry_point = $this->getFinalRoute();
      $entry_point_URL = $this->getPathValidator()->getUrlIfValid($entry_point);
      // Get the redirect route to the next form based on the flow configuration
      // 'operation' parameter that matches the submit button's name.
      $submit_action = $form_state->getTriggeringElement()['#name'];
      // Get the next route from the flow.
      $redirect_route = $this->getFlowNegotiator()->getFlow()->progressRoute($submit_action, $entry_point_URL);
    }
    catch (ParFlowException $e) {

    }
    catch (RouteNotFoundException $e) {

    }

    $url = isset($redirect_route) ? Url::fromRoute($redirect_route, $this->getRouteParams()) : NULL;
    // Set the redirection.
    if ($url && $url instanceof Url) {
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function multipleItemActionsSubmit(array &$form, FormStateInterface $form_state) {
    // Ensure that destination query params don't redirect.
    $this->selfRedirect($form_state);

    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);
  }

  /**
   * {@inheritdoc}
   */
  public function removeItem(array &$form, FormStateInterface $form_state) {
    // Ensure that destination query params don't redirect.
    $this->selfRedirect($form_state);

    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);

    list($button, $plugin_namespace, $cardinality) = explode(':', $form_state->getTriggeringElement()['#name']);
    $values = $form_state->getValue(ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_namespace);
    end($values);
    $last_index = (int) key($values);
    $component = $this->getComponent($plugin_namespace);

    $form_state->unsetValue([ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_namespace, (int) $cardinality - 1]);

    // Validate the components and remove any unvalidated last item.
    $component->validate($form, $form_state, $last_index, ParFormBuilder::PAR_ERROR_CLEAR);

    // Resave the values based on the newly removed items.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);
  }

  /**
   * Form saving handler.
   *
   * Required to be overwritten by implementing forms
   * as will currently not auto-save.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function saveForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Cancel submit handler to clear all the current flow temporary form data.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function cancelForm(array &$form, FormStateInterface $form_state) {
    try {
      // Get the cancel route from the flow.
      $route_name = $this->getFlowNegotiator()->getFlow()->progressRoute('cancel');
      $route_params = $this->getRouteParams();
    }
    catch (ParFlowException $e) {

    }
    catch (RouteNotFoundException $e) {

    }

    // @TODO, need to convert all these defaults ('getEntryUrl()',
    // 'par_dashboards.dashboard', 'skipQueryRedirection') to form listeners
    // so that they can happen within the 'progresRoute()' method and therefore
    // everywhere we call this method.


    // If no cancellation route could be found in the flow then
    // return to the entry route if one was specified.
    if (!isset($route_name) && $url = $this->getEntryUrl()) {
      $route_name = $url->getRouteName();
      $route_params = $url->getRouteParameters();
    }

    // We need a backup route in case all else fails.
    if (!isset($route_name)) {
      $route_name = 'par_dashboards.dashboard';
      $route_params = [];
    }

    // Remove the destination parameter if it redirects to a route within the flow,
    // 'cancel' and 'done' operations should exit out of the flow.
    $query = $this->getCurrentRequest()->query;
    if ($query->has('destination')) {
      $destination = $query->get('destination');
      $destination_url = $this->getPathValidator()->getUrlIfValid($destination);

      if ($destination_url && $destination_url instanceof Url && $destination_url->isRouted() && $this->getFlowNegotiator()->routeInFlow($destination_url->getRouteName())) {
        $query->remove('destination');
      }
    }

    // Delete form storage.
    $this->getFlowDataHandler()->deleteStore();

    if ($url && $url instanceof Url) {
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   * Get the route to return to once the journey has been completed.
   */
  public function getFinalRoute() {
    // Get the route that we entered on.
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);

    return $entry_point;
  }

  /**
   * A helper function to ensure in form buttons don't redirect away.
   *
   * @param $form_state
   */
  public function selfRedirect(&$form_state) {
    $options = [];
    $query = $this->getRequest()->query;
    if ($query->has('destination')) {
      $options['query']['destination'] = $query->get('destination');
      $query->remove('destination');
    }
    $form_state->setRedirect('<current>', $this->getRouteParams(), $options);
  }

  /**
   * Find form element anchor/HTML id.
   *
   * @param array $element_key
   *   The key of the form element to set the error for.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to set the error on.
   *
   * @return string $form_element_page_anchor
   *   Form element/wrapper anchor ID.
   */
  public function getFormElementPageAnchor($element_key, FormStateInterface &$form_state) {
    $form_element = &NestedArray::getValue($form_state->getCompleteForm(), $element_key);

    // Catch some potential FAPI mistakes.
    if (!isset($form_element['#type']) ||
      !isset($form_element['#id'])) {
      return false;
    }

    // Several options e.g. radios, checkboxes are appended with --wrapper.
    switch ($form_element['#type']) {

      case 'radios':
      case 'checkboxes':
        $form_element_page_anchor = $form_element['#id'] . '--wrapper';
        break;
      default:
        $form_element_page_anchor = $form_element['#id'];
        break;

    }

    return $form_element_page_anchor;

  }

  /**
   * Helper function to cleanse the drupal default values from the form values.
   *
   * @param array $data
   *   The data array to cleanse.
   *
   * @return array
   *   An array of values that represent keys to be removed from the form data.
   */
  public function cleanseFormDefaults(array $data) {
    $defaults = ['form_id', 'form_build_id', 'form_token', 'op'];
    return array_diff_key($data, array_flip(array_merge($defaults, $this->getIgnoredValues())));
  }

  /**
   * Helper function to cleanse empty multiple values for a multi value form plugin..
   *
   * @param array $data
   *   The data array to cleanse.
   *
   * @return array
   *   An array of values that represent keys to be removed from the form data.
   */
  public function cleanseMultipleValues(array $data) {
    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      $values = $data[ParFormBuilder::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()] ?? NULL;

      if ($values) {
        // Always remove the 'remove' link.
        foreach ($values as $cardinality => $value) {
          if (isset($value['remove'])) {
            unset($value['remove']);
          }

          $value = NestedArray::filter($value);

          $values[$cardinality] = array_filter($value, function ($value, $key) use ($component) {
            $default_value = $component->getFormDefaultByKey($key);
            if (empty($value)) {
              return FALSE;
            }

            if (!$default_value) {
              return TRUE;
            }

            return $default_value !== $value;
          }, ARRAY_FILTER_USE_BOTH);
        }

        $data[ParFormBuilder::PAR_COMPONENT_PREFIX . $component->getPluginNamespace()] = NestedArray::filter($values);
      }

    }

    return $data;
  }

  /**
   * Helper to decide whether what the entity value should be
   * saved as for a boolean input.
   *
   * @param mixed $input
   *   The input value to check.
   * @param mixed $on
   *   The expected value of the on state.
   * @param mixed $off
   *   The expected value of the off state.
   *
   * @return mixed
   *   The new value for the entity.
   */
  public function decideBooleanValue($input, $on = 'on', $off = 'off') {
    return ($on === $input || $input === TRUE) ? TRUE : FALSE;
  }

}
