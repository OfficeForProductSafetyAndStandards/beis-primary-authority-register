<?php

namespace Drupal\par_flows\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowDataHandler;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_forms\ParEntityValidationMappingTrait;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginInterface;
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
   * Do not serialize the components, they will be fetched as required.
   */
  public function __sleep() {
    $ignore = ['components'];
    return array_diff(parent::__sleep(), $ignore);
  }

  /**
   * The access result.
   *
   * @var ?AccessResult $accessResult
   */
  protected ?AccessResult $accessResult = NULL;

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
   * @var array $formItems
   */
  protected array $formItems = [];

  /**
   * The key values to be ignored from form submissions.
   *
   * @var ?array $ignoreValues
   */
  protected ?array $ignoreValues;

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
  public function __construct(ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler, ParDataManagerInterface $par_data_manager, PluginManagerInterface $plugin_manager, UrlGeneratorInterface $url_generator) {
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
    $this->parDataManager = $par_data_manager;
    $this->formBuilder = $plugin_manager;
    $this->urlGenerator = $url_generator;

    $this->setCurrentUser();

    // @TODO Move this to middleware to stop it being loaded when this controller
    // is constructed outside a request for a route this controller resolves.
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
      $container->get('plugin.manager.par_form_builder'),
      $container->get('url_generator')
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
  public function getIgnoredValues(): array {
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
   * @param ?array $values
   *   Configure additional key names to be removed from form data.
   */
  public function setIgnoredValues(?array $values = NULL): void {
    if (isset($values)) {
      $this->ignoreValues = $values;
    }
  }

  /**
   *  Default access callback.
   *
   * @param Route $route
   *   The route.
   * @param RouteMatchInterface $route_match
   *   The route match object to be checked.
   * @param AccountInterface $account
   *   The account being checked.
   *
   * @return AccessResult
   *   The access result.
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account): AccessResult {
    return $this->accessResult instanceof AccessResult ? $this->accessResult : AccessResult::allowed();
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

    // Attach the JS form libraries.
    $form['#attached']['library'][] = 'par_flows/flow_core';

    // Set the default submission handlers, some components may alter this.
    $primary_submit_handers = ['::submitForm'];
    $secondary_submit_handlers = [];

    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      // Get the index value to alter the display of elements.
      $index_key = ['_index', $component->getPrefix()];
      $index = $form_state->getValue($index_key);

      // Build the plugin.
      $plugin = $this->getFormBuilder()->build($component, $index);

      // Handle instances where the form component plugin returns a redirect response.
      if ($plugin instanceof RedirectResponse) {
        return $plugin;
      }

      // Components that support the summary list component but are displaying
      // the form elements will self-submit to render the summary list.
      if ($this->getFormBuilder()->supportsSummaryList($component) &&
        !$this->getFormBuilder()->displaySummaryList($component, $index)) {

        // Only change the primary submit handler if the summary list is not displayed.
        $primary_submit_handers = array_merge(['::selfRedirect'], $primary_submit_handers);

        // Only change the secondary submit handler if there is data.
        if (!empty($component->getData())) {
          $secondary_submit_handlers = array_merge(['::selfRedirect'], $secondary_submit_handlers);
        }
      }

      // Merge the component elements into the form array.
      $form = array_merge($form, $plugin);
    }

    // Enable the default actions wrapper.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // The 'done' is a primary and final action, meaning no other actions should be performed.
    // The 'upload', 'save' and 'next are all primary actions with varying subtleties.
    // The 'back' and 'cancel' are secondary actions and can complement a primary action.

    // The done action completes the flow without performing any changes,
    // removing any remaining persistent data in the process.
    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $form['actions']['done'] = [
        '#type' => 'submit',
        '#name' => 'done',
        '#button_type' => 'primary',
        '#submit' => $primary_submit_handers,
        '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Done'),
        '#limit_validation_errors' => [],
        '#attributes' => [
          'class' => ['cta-submit', 'govuk-button'],
          'data-prevent-double-click' => 'true',
          'data-module' => 'govuk-button',
        ],
      ];
    }
    else {
      // The upload button indicates that file uploads are being handled,
      // usually within a flow and progressing to the next step.
      if ($this->getFlowNegotiator()->getFlow()->hasAction('upload')) {
        $form['actions']['upload'] = [
          '#type' => 'submit',
          '#name' => 'upload',
          '#button_type' => 'primary',
          '#submit' => $primary_submit_handers,
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Upload'),
          '#attributes' => [
            'class' => ['cta-submit', 'govuk-button'],
            'data-prevent-double-click' => 'true',
            'data-module' => 'govuk-button',
          ],
        ];
      }
      // The save button is meant to indicate the step makes permanent changes,
      // usually with the effect of completing the flow and redirecting onwards.
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('save')) {
        array_push($primary_submit_handers, '::saveForm');
        $form['actions']['save'] = [
          '#type' => 'submit',
          '#name' => 'save',
          '#button_type' => 'primary',
          '#submit' => $primary_submit_handers,
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Save'),
          '#attributes' => [
            'class' => ['cta-submit', 'govuk-button'],
            'data-prevent-double-click' => 'true',
            'data-module' => 'govuk-button',
          ],
        ];
      }
      // The next action is designed to continue to the next step without
      // processing any data.
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $form['actions']['next'] = [
          '#type' => 'submit',
          '#name' => 'next',
          '#button_type' => 'primary',
          '#submit' => $primary_submit_handers,
          '#value' => $this->getFlowNegotiator()->getFlow()->getPrimaryActionTitle('Continue'),
          '#attributes' => [
            'class' => ['cta-submit', 'govuk-button'],
            'data-prevent-double-click' => 'true',
            'data-module' => 'govuk-button',
          ],
        ];
      }

      // The cancel action is designed to cancel out of the flow completely.
      // Removing any persistent flow data in the process.
      if ($this->getFlowNegotiator()->getFlow()->hasAction('cancel')) {
        $form['actions']['cancel'] = [
          '#type' => 'submit',
          '#name' => 'cancel',
          '#submit' => !empty($secondary_submit_handlers) ? $secondary_submit_handlers : ['::cancelForm'],
          '#value' => $this->getFlowNegotiator()->getFlow()->getSecondaryActionTitle('Cancel'),
          '#validate' => ['::validateCancelForm'],
          '#limit_validation_errors' => [],
          '#attributes' => [
            'class' => ['cta-cancel', 'govuk-button', 'govuk-button--secondary']
          ],
        ];
      }
      // The back action is a lesser version of the cancel action regressing
      // back a step but without removing any persistent data.
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('back')) {
        $form['actions']['back'] = [
          '#type' => 'submit',
          '#name' => 'back',
          '#submit' => !empty($secondary_submit_handlers) ? $secondary_submit_handlers : ['::backForm'],
          '#value' => $this->getFlowNegotiator()->getFlow()->getSecondaryActionTitle('Back'),
          '#validate' => ['::validateCancelForm'],
          '#limit_validation_errors' => [],
          '#attributes' => [
            'class' => ['cta-back', 'govuk-button', 'govuk-button--secondary']
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
   * A false validation handler.
   */
  public function validateCancelForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate all the plugins first.
    foreach ($this->getComponents() as $component) {
      // Get the index value to validate the component for.
      $index_key = ['_index', $component->getPrefix()];
      $index = $form_state->getValue($index_key);

      // Validate the component
      $this->getFormBuilder()->validate($component, $form, $form_state, $index);
    }

    // Validate all the form elements.
    // @TODO @deprecated All forms should use form plugin components going forward.
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

    // Store values post validation if there were no errors to ensure plugins
    // can manipulate form data.
    if (!$form_state->hasAnyErrors()) {
      $this->storeData($form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $this->storeData($form_state);

    try {
      // Get the redirect route to the next form based on the flow configuration
      // 'operation' parameter that matches the submit button's name.
      $submit_action = $form_state->getTriggeringElement() ?
        $form_state->getTriggeringElement()['#name'] : NULL;

      // Get the next route from the flow.
      $url = $this->getFlowNegotiator()->getFlow()->progress($submit_action);
    }
    catch (ParFlowException | RouteNotFoundException $e) {

    }

    // Delete form storage if complete.
    if (isset($submit_action) && $submit_action == 'done') {
      $this->getFlowDataHandler()->deleteStore();
    }

    // Set the redirection.
    if (isset($url) && $url instanceof Url) {
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addAnother(array &$form, FormStateInterface $form_state) {
    // Rebuild the form rather than redirect to ensure that form state values are persisted.
    $this->selfRedirect($form, $form_state, TRUE);

    // Loop through all the components and update the cardinality value.
    foreach ($this->getComponents() as $component) {
      if ($component->isMultiple()) {
        // Get the index value to alter the display of elements.
        $index_key = ['_index', $component->getPrefix()];
        $new_index = $component->getNextAvailableIndex();
        $form_state->setValue($index_key, $new_index);
      }
    }
  }

  public function changeItem(array &$form, FormStateInterface $form_state) {
    // Rebuild the form rather than redirect to ensure that form state values are persisted.
    $this->selfRedirect($form, $form_state, TRUE);

    $triggering_element = $form_state->getTriggeringElement() ?
      $form_state->getTriggeringElement()['#name'] : NULL;
    if ($triggering_element) {
      [$button, $plugin_namespace, $index] = explode(':', $triggering_element);
    }

    // Get the component.
    $component = $this->getComponent($plugin_namespace);

    // Items can only be changed if the cardinality allows for multiple elements.
    if (!$component instanceof ParFormPluginInterface || !$component->isMultiple()) {
      return;
    }

    // Set the index for the item to update.
    $index_key = ['_index', $component->getPrefix()];
    $form_state->setValue($index_key, $index);
  }

  /**
   * {@inheritdoc}
   */
  public function removeItem(array &$form, FormStateInterface &$form_state) {
    // Ensure that destination query params don't redirect.
    $this->selfRedirect($form, $form_state, FALSE);

    $triggering_element = $form_state->getTriggeringElement() ?
      $form_state->getTriggeringElement()['#name'] : NULL;
    if ($triggering_element) {
      [$button, $plugin_namespace, $index] = explode(':', $triggering_element);
    }

    // Get the component.
    $component = $this->getComponent($plugin_namespace);

    // Items can only be removed from multiple cardinality components.
    if (!$component instanceof ParFormPluginInterface || !$component->isMultiple()) {
      return;
    }

    $delta = (int) $index - 1;

    // Get the data.
    $data = $component->getData();

    // Unset the value.
    unset($data[$delta]);
    // Unset the form state value.
    $item_key = [ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_namespace, (int) $delta];
    if ($form_state->hasValue($item_key)) {
      $form_state->unsetValue($item_key);
      $form_state->setValue($item_key, NULL);
    }

    // Store the value.
    $component->setData($data);

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
      $url = $this->getFlowNegotiator()->getFlow()->progress('cancel');
    }
    catch (ParFlowException $e) {

    }
    catch (RouteNotFoundException $e) {

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
   * Back submit handler to progress back preserving the current flow temporary form data.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function backForm(array &$form, FormStateInterface $form_state) {
    try {
      // Get the cancel route from the flow.
      $url = $this->getFlowNegotiator()->getFlow()->progress('back');
    }
    catch (ParFlowException $e) {

    }
    catch (RouteNotFoundException $e) {

    }

    if ($url && $url instanceof Url) {
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   * Clean the submitted values from the form state.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   *   The cleaned form state values.
   */
  public function cleanFormState(FormStateInterface $form_state): array {
    // Remove non-user submitted form values.
    $submitted_values = $form_state->cleanValues()->getValues();

    // Remove the triggering element button from the form_state.
    $triggering_element = $form_state->getTriggeringElement() ?
      $form_state->getTriggeringElement()['#name'] : NULL;
    if (isset($submitted_values[$triggering_element])) {
      unset($submitted_values[$triggering_element]);
    }

    // Filter out empty values.
    return $this->cleanValues($submitted_values);
  }

  /**
   * Helper function to cleanse empty multiple values for a multi value form plugin.
   *
   * @param array $data
   *   The data array to cleanse.
   *
   * @return array
   *   An array of values that represent keys to be removed from the form data.
   */
  public function cleanValues(array $data) {
    // Filter out empty data from each form component.
    foreach ($this->getComponents() as $component) {
      if ($component->isFlattened()) {
        $component_data =& $data;
      }
      else {
        $component_data =& $data[$component->getPrefix()];
      }

      // Only filter if there is some component data submitted.
      if (!empty($component_data)) {
        $component_data = $component->filter($component_data);
      }
    }

    // Remove all final empty values.
    return $this->getFlowDataHandler()->filter($data, [ParFlowDataHandler::class, 'filterValues']);
  }

  /**
   * Save the form data.
   */
  protected function storeData(FormStateInterface $form_state) {
    $submitted_values = $this->cleanFormState($form_state);

    if (!empty($submitted_values)) {
      // Merge data with existing values.
      $data = $this->mergeData($submitted_values);

      // Reindex all data multiple cardinality plugin data.
      $this->reindexData($data);

        // Set the new form values.
      $this->getFlowDataHandler()->setFormTempData($data);
    }
  }

  /**
   * Merge the data with any existing values in the temporary data store.
   *
   * @param $data
   *   The data to be added.
   *
   * @return array
   *   The merged data.
   */
  protected function mergeData($data): array {
    // Combine with any data already submitted.
    $existing_data = $this->getFlowDataHandler()->getFormTempData();

    // For components that support the summary list only add the data that has been changed.
    foreach ($this->getComponents() as $component) {
      // Don't merge any component data if the component has singular cardinality or is empty.
      if (!$component->isMultiple() || !$component->hasData()) {
        continue;
      }

      // For summary lists ensure only the indexes being modified are added.
      if ($this->getFormBuilder()->supportsSummaryList($component)) {
          $data[$component->getPrefix()] = $data[$component->getPrefix()] + $existing_data[$component->getPrefix()];
      }
      // There are no other situations where existing structured component data is persisted.
      unset($existing_data[$component->getPrefix()]);
    }

    // Ensure that any data outside the components can be maintained in the existing data.
    return NestedArray::mergeDeepArray([$existing_data, $data], TRUE);
  }

  /**
   * Merge the data with any existing values in the temporary data store.
   *
   * @param $data
   *   The data to be added.
   *
   * @return array
   *   The merged data.
   */
  protected function reindexData($data): array {
    // For components that support multiple cardinality allow the values to be reindexed.
    foreach ($this->getComponents() as $component) {
      // Don't reindex any components that have singular cardinality or are empty.
      if (!$component->isMultiple() || !$component->hasData()) {
        continue;
      }

      // Filter the data.
      $data = $this->getFlowDataHandler()->filter($data, [ParFlowDataHandler::class, 'filterValues']);

      // Reinded the data.
      $data[$component->getPrefix()] = array_values($data[$component->getPrefix()]);
    }

    // Filter empty values from merged data also.
    return $data;
  }

  /**
   * Get the route to return to once the journey has been completed.
   */
  public function getFinalRoute() {
    // Get the route that we entered on.
    return $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);
  }

  /**
   * Get the route to return to once the journey has been completed.
   */
  public function geFlowEntryURL() {
    // Get the route that we entered on.
    $entry_point = $this->getFlowDataHandler()->getMetaDataValue(ParFlowDataHandler::ENTRY_POINT);
    try {
      $url = $this->getPathValidator()->getUrlIfValid($entry_point);
    }
    catch (\InvalidArgumentException $e) {

    }

    if ($url && $url instanceof Url && $url->isRouted()) {
      return $url;
    }
    return NULL;
  }

  /**
   * A helper function to ensure in form buttons don't redirect away.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function selfRedirect(array &$form, FormStateInterface $form_state, bool $rebuild = TRUE) {
    // Setting a redirection allows form values to be cleared.
    $options = [];
    $query = $this->getRequest()->query;
    if ($query->has('destination')) {
      $options['query']['destination'] = $query->get('destination');
      $query->remove('destination');
    }
    $params = $this->getRouteParams();

    // If $rebuild is set the form will simply be rebuilt.
    if ($rebuild) {
      $form_state->setRebuild(TRUE);
    }
    // There are some instances, however, where forms must be self-redirected.
    else {
      $form_state->setRedirect('<current>', $params, $options);
    }
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

  /**
   * Return the date formatter service.
   *
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

}
