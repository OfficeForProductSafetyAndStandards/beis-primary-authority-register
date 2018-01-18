<?php

namespace Drupal\par_flows\Form;

use Drupal\Core\Cache\RefinableCacheableDependencyTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Entity\ParFlow;
use Drupal\par_flows\ParBaseInterface;
use Drupal\par_flows\ParControllerTrait;
use Drupal\par_flows\ParFlowException;
use Drupal\user\PrivateTempStoreFactory;
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
   * The Drupal session manager.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * The flow entity storage class, for loading flows.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $flowStorage;

  /**
   * The private temporary storage for persisting multi-step form data.
   *
   * Each key (form) will last 1 week since it was last updated.
   *
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * Caches the values of any data loaded from the permanent store.
   *
   * @var array
   */
  protected $data = [];

  /**
   * A machine safe value representing the current form journey.
   *
   * @var string
   */
  protected $flow;

  /**
   * A machine name representing any state(s) affecting the form behaviour.
   *
   * Example: A example of a state would be whether the flow is being created,
   * edited or reviewed.
   *
   * @var string
   */
  protected $state = 'default';

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

  /**
   * Page title.
   *
   * @var string
   */
  protected $pageTitle;

  /*
   * Constructs a \Drupal\par_flows\Form\ParBaseForm.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The private temporary store.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user object.
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The current user object.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user, ConfigEntityStorageInterface $flow_storage, ParDataManagerInterface $par_data_manager) {
    $this->sessionManager = $session_manager;
    $this->setCurrentUser($current_user);
    $this->flowStorage = $flow_storage;
    $this->parDataManager = $par_data_manager;
    /** @var \Drupal\user\PrivateTempStore store */
    $this->store = $temp_store_factory->get('par_flows_flows');

    // If no flow entity exists throw a build error.
    if (!$this->getFlow()) {
      $this->getLogger($this->getLoggerChannel())->critical('There is no flow %flow for this form.', ['%flow' => $this->getFlowName()]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user'),
      $entity_manager->getStorage('par_flow'),
      $container->get('par_data.manager')
    );
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    if (empty($this->pageTitle) &&
        $default_title = $this->getFlow()->getDefaultTitle()) {
      return $default_title;
    }

    // Do we have a form flow subheader?
    if (!empty($this->getFlow()->getDefaultSectionTitle() &&
        !empty($this->pageTitle))) {
      $this->pageTitle = "{$this->getFlow()->getDefaultSectionTitle()} | {$this->pageTitle}";
    }

    return $this->pageTitle;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['user.roles', 'route'];
  }

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName() {
    if (empty($this->flow)) {
      throw new ParFlowException('The flow must have a name.');
    }
    return $this->flow;
  }

  /**
   * Get the current flow entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The flow entity.
   */
  public function getFlow() {
    return $this->getFlowStorage()->load($this->getFlowName());
  }

  /**
   * Get the injected Flow Entity Storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The flow storage handler.
   */
  public function getFlowStorage() {
    return $this->flowStorage;
  }

  /**
   * Get the current flow state.
   *
   * @return string
   *   The string representing the current state of the flow.
   */
  public function getState() {
    return isset($this->state) ? $this->state : '';
  }

  /**
   * Set the current flow state.
   *
   * @param string $state
   *   The string to use to differentiate flow states.
   */
  public function setState(string $state) {
    if (isset($state)) {
      $this->state = $state;
    }
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
    // Only ever place a 'done' action by itself.
    if ($this->getFlow()->hasAction('done')) {
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
      if ($this->getFlow()->hasAction('upload')) {
        $form['actions']['upload'] = [
          '#type' => 'submit',
          '#name' => 'upload',
          '#value' => $this->t('Upload'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }
      elseif ($this->getFlow()->hasAction('save')) {
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
      elseif ($this->getFlow()->hasAction('next')) {
        $form['actions']['next'] = [
          '#type' => 'submit',
          '#name' => 'next',
          '#value' => $this->t('Continue'),
          '#attributes' => [
            'class' => ['cta-submit']
          ],
        ];
      }

      if ($this->getFlow()->hasAction('cancel')) {
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
          $violations = $entity->validate()->filterByFieldAccess()
            ->getByFields([
              $field_name,
            ]);

          $this->setFieldViolations($field_name, $form_state, $violations);
        }
        catch(\Exception $e) {
          $this->getLogger($this->getLoggerChannel())->critical('An error occurred validating form %form_id: @detail.', ['%form_id' => $this->getFormId(), '@details' => $e->getMessage()]);
          $form_state->setError($form, 'An error occurred while checking your submission, please contact the helpdesk if this problem persists.');
        }
      }
    }

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
          $arguments = is_string($replacements) ? ['@field' => $replacements] : $replacements;
        }
        else {
          $arguments = ['@field' => $field_label];
        }
        $message = $this->t($violation->getMessage()->render(), ['@field' => $field_label]);

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
    $this->setFormTempData($form_state->getValues());

    $submit_action = $form_state->getTriggeringElement()['#name'];
    $next = $this->getFlow()->getNextRoute($submit_action);
    $form_state->setRedirect($next, $this->getRouteParams());
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
    $this->deleteStore();

    // Go to cancel step.
    $next = $this->getFlow()->getPrevRoute('cancel');
    $form_state->setRedirect($next, $this->getRouteParams());
  }

  /**
   * Cancel submit handler to clear the current temporary form data.
   *
   * @code
   * $form['actions']['previous'] = [
   *   '#type' => 'submit',
   *   '#name' => 'previous',
   *   '#value' => $this->t('Previous'),
   *   '#submit' => ['::cancelThisForm'],
   * ];
   * @endcode
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function cancelThisForm(array &$form, FormStateInterface $form_state) {
    // Delete specific form storage.
    $this->deleteFormTempData($this->getFormId());

    // Go to cancel step.
    $next = $this->getFlow()->getPrevRoute('cancel');
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
   * Get default values for a given form element.
   *
   * This will first look at the temporary store for a value.
   * Then it will look in the permanent store for any saved value.
   * And finally it will use the default value if specified.
   *
   * If self::isStubbed returns true then the permanent store
   * will not be used to lookup any values.
   *
   * @param string|array $key
   *   The key to search for.
   * @param mixed $default
   *   The default value to return if no value was found.
   * @param string $form_id
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return mixed|null
   *   The value for this key.
   */
  protected function getDefaultValues($key, $default = '', $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    $value = $this->getTempDataValue($key, $form_id);

    if (!$value) {
      $value = $this->getDataValue($key);
    }

    if (!$value) {
      $value = $default;
    }

    return $value;
  }

  /**
   * Get a value from the temp data store for a form element.
   *
   * @param string|array $key
   *   The key to search for.
   * @param string $form_id
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return mixed|null
   *   The value for this key.
   */
  protected function getTempDataValue($key, $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    $tmp_data = $this->getFormTempData($form_id);

    $value = NestedArray::getValue($tmp_data, (array) $key, $exists);

    return $value;
  }

  /**
   * Set a value for a form element in the temporary store.
   *
   * @param string $key
   *   The key to search for.
   * @param mixed $value
   *   The value to store for this key. Can be any string, integer or object.
   * @param string $form_id
   *   The form_id to get data for, will use the current form if not set.
   */
  protected function setTempDataValue($key, $value, $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();
    $data = $this->getFormTempData($form_id);
    NestedArray::setValue($data, (array) $key, $value, TRUE);
    $this->setFormTempData($data, $form_id);
  }

  /**
   * Retrieve the temporary data for a form.
   *
   * @param string $form_id
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return array
   *   The values stored in the temp store.
   */
  protected function  getFormTempData($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $data = $this->store->get($this->getFormKey($form_id));

    return $data ?: [];
  }

  /**
   * Retrieve the temporary data for a form.
   *
   * @param array $data
   *   The array of data to be saved.
   * @param string $form_id
   *   The form_id to set data for, will use the current form if not set.
   */
  protected function setFormTempData(array $data, $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    if (!$data || !is_array($data)) {
      return;
    }

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $this->store->set($this->getFormKey($form_id), $data);
  }

  /**
   * Delete the temporary data for a form.
   *
   * @param string $form_id
   *   The form_id to set data for, will use the current form if not set.
   */
  protected function deleteFormTempData($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    $this->store->delete($this->getFormKey($form_id));
  }

  /**
   * Get all the data for the current flow state.
   */
  protected function getAllTempData() {
    $data = [];

    foreach ($this->getFlow()->getFlowForms() as $form) {
      $form_data = $this->getFormTempData($form);
      $data = array_merge($data, $this->cleanseFormDefaults($form_data));
    }

    return $data;
  }

  /**
   * Delete all the data for the current flow state.
   */
  protected function deleteStore() {
    $data = [];

    foreach ($this->getFlow()->getFlowForms() as $form) {
      $form_data = $this->deleteFormTempData($form);
    }

    return $data;
  }

  /**
   * Get the saved value for a form element from the local cache.
   *
   * This does not load the value from the entity.
   * It only access values from the local cache.
   * These values must be populated first with
   * self::loadDataValue before accessing.
   *
   * @param string|array $key
   *   The key of the element to search for.
   *
   * @return mixed|null
   *   The value for this element
   */
  protected function getDataValue($key) {
    return NestedArray::getValue($this->data, (array) $key);
  }

  /**
   * Load a saved value into the local cache.
   *
   * The value must first be looked up using the
   * relevant entity/field storage lookup methods.
   *
   * @param string $key
   *   The key to search for.
   * @param mixed $value
   *   The value to store for this key. Can be any string, integer or object.
   * @param mixed $stubbed
   *   The stubbed value to use instead .
   */
  protected function loadDataValue($key, $value) {
    NestedArray::setValue($this->data, (array) $key, $value, TRUE);
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
   * Get the form Key.
   *
   * @param string $form_id
   *   An optional form_id to get the key for.
   *
   * @return string
   *   The name of the key for the given form.
   */
  public function getFormKey($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();
    $key = implode('_', [$this->getFlowName(), $this->getState(), $form_id]);
    return $this->normalizeKey($key);
  }

  /**
   * Normalizes a cache ID in order to comply with key length limitations.
   *
   * @param string $key
   *   The passed in cache ID.
   *
   * @return string
   *   An ASCII-encoded cache ID that is at most 64 characters long.
   */
  public function normalizeKey($key) {
    $key = urlencode($key);
    // Nothing to do if the ID is a US ASCII string of 64 characters or less.
    $key_is_ascii = mb_check_encoding($key, 'ASCII');
    if (strlen($key) <= 64 && $key_is_ascii) {
      return $key;
    }

    // If we have generated a longer key, we shrink it to an
    // acceptable length with a configurable hashing algorithm.
    // Sha1 was selected as the default as it performs
    // quickly with minimal collisions.
    //
    // Return a string that uses as much as possible of the original cache ID
    // with the hash appended.
    $hash = hash('sha1', $key);
    if (!$key_is_ascii) {
      return $hash;
    }
    return substr($key, 0, 64 - strlen($hash)) . $hash;
  }

  /**
   * Start a manual session for anonymous users.
   */
  public function startAnonymousSession() {
    if ($this->getCurrentUser()->isAnonymous() && !isset($_SESSION['session_started'])) {
      $_SESSION['session_started'] = TRUE;
      $this->sessionManager->start();
    }
  }

}
