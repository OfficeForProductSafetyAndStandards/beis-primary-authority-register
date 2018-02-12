<?php

namespace Drupal\par_flows\Form;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Drupal\par_forms\ParFormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;
use Drupal\par_flows\ParRedirectTrait;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\Core\Access\AccessResult;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParBaseForm extends FormBase implements ParBaseInterface {

  use ParRedirectTrait;
  use RefinableCacheableDependencyTrait;
  use ParDisplayTrait;
  use StringTranslationTrait;
  use ParControllerTrait;

  /**
   * The access result
   *
   * @var \Drupal\Core\Access\AccessResult
   */
  protected $accessResult;

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
   * @see \Drupal\Core\Access\AccessResult
   *   The options for callback.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function accessCallback() {
    return $this->accessResult ? $this->accessResult : AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      $form = $this->getFormBuilder()->getPluginElements($component);
    }

    // Only ever place a 'done' action by itself.
    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $form['actions']['done'] = [
        '#type' => 'submit',
        '#name' => 'done',
        '#value' => $this->t('Done'),
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
          '#value' => $this->t('Upload'),
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
          '#value' => $this->t('Save'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }
      elseif ($this->getFlowNegotiator()->getFlow()->hasAction('next')) {
        $form['actions']['next'] = [
          '#type' => 'submit',
          '#name' => 'next',
          '#value' => $this->t('Continue'),
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

    // Add all the registered components to the form.
    foreach ($this->getComponents() as $component) {
      $component_violations = $this->getFormBuilder()->validatePluginElements($component, $form_state);

      // If there are violations for this plugin.
      if (isset($component_violations[$component->getPluginId()])) {
        foreach ($component_violations[$component->getPluginId()] as $cardinality => $violations) {
          foreach ($violations as $field_name => $violation_list) {
            // Do not validate the last item if multiple cardinality is allowed.
            if ($violation_list->count() >= 1) {
              // Validate the first item, and all but the last items thereafter.
              // @example 1st item out of 1 should be validated.
              // @example 1st item out of 3 should be validated.
              // @example 2nd item out of 3 should be validated.
              // @example 3rd item out of 3 should _not_ be validated.
              if ($component->getCardinality() === 1 || $cardinality === 1 || $cardinality < $component->countItems()) {
                $this->setFieldViolations($field_name, $form_state, $violation_list);
              }
            }
          }
        }
      }
    }

    // @TODO Remove this method once/if all forms use components.
    if (!empty($this->getFormItems())) {
      $form_violations = $this->validateElements($form_state);
      if ($form_violations) {
        foreach ($form_violations as $field_name => $violation_list) {
          $this->setFieldViolations($field_name, $form_state, $violation_list);
        }
      }
    }
  }

  public function validateElements($form_state) {
    $violations = [];

    // Assign all the form values to the relevant entity field values.
    foreach ($this->getformItems() as $entity_name => $form_items) {
      list($type, $bundle) = explode(':', $entity_name . ':');

      $entity_class = $this->getParDataManager()->getParEntityType($type)->getClass();
      $entity = $entity_class::create([
        'type' => $this->getParDataManager()->getParBundleEntity($type, $bundle)->id(),
      ]);

      foreach ($form_items as $field_name => $form_item) {
        $field_definition = $this->getParDataManager()->getFieldDefinition($entity->getEntityTypeId(), $entity->bundle(), $field_name);

        if (is_array($form_item)) {
          $field_value = [];
          foreach ($form_item as $field_property => $form_property_item) {
            // For entity reference fields we need to transform the ids to integers.
            if ($field_definition->getType() === 'entity_reference' && $field_property === 'target_id') {
              $field_value[$field_property] = (int) $form_state->getValue($form_property_item);
            }
            else {
              $field_value[$field_property] = $form_state->getValue($form_property_item);
            }
          }
        }
        else {
          $field_value = $form_state->getValue($form_item);
        }

        $entity->set($field_name, $field_value);

        try {
          $violations[$field_name] = $entity->validate()->filterByFieldAccess()
            ->getByFields([
              $field_name,
            ]);
        }
        catch(\Exception $e) {
          $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %entity_id: @detail.', ['%entity_id' => $entity->getEntityTypeId(), '@details' => $e->getMessage()]);
        }
      }
    }

    return $violations;
  }

  /**
   * Set the errors for a given field based on entity violations.
   *
   * @param mixed $name
   *   The name of the form element to set the error for.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to set the error on.
   * @param \Drupal\Core\Entity\EntityConstraintViolationListInterface $violations
   *   The violations to set.
   * @param array $replacements
   *   An optional array of message replacement arguments.
   */
  public function setFieldViolations($name, FormStateInterface &$form_state, EntityConstraintViolationListInterface $violations, $replacements = NULL) {
    $name = (array) $name;

    if ($violations) {
      foreach ($violations as $violation) {
        $fragment = $this->getFormElementPageAnchor($name, $form_state);
        $options = [
          'fragment' => $fragment,
        ];

        $field_label = end($name);
        if (!empty($replacements)) {
          $arguments = is_string($replacements) ? ['@name' => $replacements, '@field' => $replacements] : $replacements;
        }
        else {
          $arguments = ['@name' => $field_label, '@field' => $field_label];
        }
        $message = $this->t($violation->getMessage()->getUntranslatedString(), $arguments);

        $url = Url::fromUri('internal:#', $options);
        $link = Link::fromTextAndUrl($message, $url)->toString();

        $form_state->setErrorByName($field_label, $link);
      }
    }
  }

  /**
   * Set the errors for a given field.
   *
   * @param mixed $name
   *   The name of the form element to set the error for.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to set the error on.
   * @param string $message
   *   The message to set for this element.
   * @param array $replacements
   *   An optional array of message replacement arguments.
   */
  public function setElementError($name, FormStateInterface &$form_state, $message, $replacements = NULL) {
    $name = (array) $name;

    $fragment = $this->getFormElementPageAnchor($name, $form_state);
    $options = [
      'fragment' => $fragment,
    ];

    $field_label = end($name);
    if (!empty($replacements)) {
      $arguments = is_string($replacements) ? ['@field' => $replacements] : $replacements;
    }
    else {
      $arguments = ['@field' => $field_label];
    }
    $message = $this->t($message, $arguments)->render();

    $url = Url::fromUri('internal:#', $options);
    $link = Link::fromTextAndUrl($message, $url)->toString();

    $form_state->setErrorByName($field_label, $link);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);

    // Set the redirect to the next form based on the flow configuration 'operation'
    // parameter that matches the submit button's name.
    $submit_action = $form_state->getTriggeringElement()['#name'];
    $next = $this->getFlowNegotiator()->getFlow()->getNextRoute($submit_action);
    $form_state->setRedirect($next, $this->getRouteParams());
  }

  /**
   * {@inheritdoc}
   */
  public function multipleItemActionsSubmit(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);
  }

  /**
   * {@inheritdoc}
   */
  public function removeItem(array &$form, FormStateInterface $form_state) {
    // Always store the values whenever we submit the form.
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $values = $this->cleanseMultipleValues($values);
    $this->getFlowDataHandler()->setFormTempData($values);

    list($button, $plugin_id, $cardinality) = explode(':', $form_state->getTriggeringElement()['#name']);
    $values = $form_state->getValue(ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_id);
    end($values);
    $last_index = (int) key($values);
    $component = $this->getComponent($plugin_id);

    $form_state->unsetValue([ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_id, (int) $cardinality - 1]);

    // If the last item does not validate we must remove this too.
    $violations = $component->validate($form_state, $last_index);
    foreach ($violations as $field_name => $violation_list) {
      if ($component && $violation_list->count() >= 1) {
        $form_state->unsetValue([ParFormBuilder::PAR_COMPONENT_PREFIX . $plugin_id, $last_index]);
      }
    }

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
    // Delete form storage.
    $this->getFlowDataHandler()->deleteStore();

    // Go to cancel step.
    $next = $this->getFlowNegotiator()->getFlow()->getPrevRoute('cancel');
    $form_state->setRedirect($next, $this->getRouteParams());
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
      $values = isset($data[ParFormBuilder::PAR_COMPONENT_PREFIX . $component->getPluginId()]) ?
        $data[ParFormBuilder::PAR_COMPONENT_PREFIX . $component->getPluginId()] :
        NULL;

      if ($values) {
        // Always remove the 'remove' link.
        foreach ($values as $cardinality => $value) {
          if (isset($value['remove'])) {
            unset($value['remove']);
          }

          $values[$cardinality] = NestedArray::filter($value);
        }

        $data[ParFormBuilder::PAR_COMPONENT_PREFIX . $component->getPluginId()] = NestedArray::filter($values);
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
