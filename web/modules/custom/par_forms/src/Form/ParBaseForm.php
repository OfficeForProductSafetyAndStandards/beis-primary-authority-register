<?php

namespace Drupal\par_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParBaseForm extends FormBase {

  /**
   * The Drupal session manager.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * The current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

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
  protected $ignoreValues = ['save', 'next', 'cancel'];

  /**
   * Constructs a \Drupal\par_forms\Form\ParBaseForm.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The private temporary store.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user object.
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $flow_storage
   *   The flow entity storage handler.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user, ConfigEntityStorageInterface $flow_storage) {
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;
    $this->flowStorage = $flow_storage;
    /** @var \Drupal\user\PrivateTempStore store */
    $this->store = $temp_store_factory->get('par_forms_flows');

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
      $entity_manager->getStorage('par_form_flow')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->setTempData($form_state->getValues());
    $form_state->setRedirect($this->getNextStep());
  }

  /**
   * Go to next step.
   *
   * @return array
   *   An array containing details of the next configured step.
   */
  public function getNextStep() {
    $flow = $this->getFlow();
    // Lookup the current step to more accurately determine the next step.
    $current_step = $flow->getStepByFormId($this->getFormId());
    $next_step = isset($current_step['step']) ? $flow->getStep(++$current_step['step']) : $flow->getStep(1);

    // If there is no next step we'll stay on this step.
    return isset($next_step['route']) ? $next_step['route'] : $current_step['route'];
  }

  /**
   * Get specific value from the temp data store for this form.
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
  protected function getDataValue($key, $default = '', $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();
    $exists = NULL;
    $data = $this->getTempData();
    $value = NestedArray::getValue($data, (array) $key, $exists);
    if (!$exists) {
      $value = $default;
    }

    $message = 'Data item %item has been retrieved for user %user from the temporary storage %key';
    $replacements = [
      '%user' => $this->currentUser->getUsername(),
      '%key' => $this->getFormKey(),
      '%item' => $key,
    ];
    $this->getLogger($this->getLoggerChannel())->debug($message, $replacements);

    return $value;
  }

  /**
   * Wraps call to static NestedArray::setValue to make more testable.
   *
   * @param string $key
   *   The key to search for.
   * @param mixed $value
   *   The value to store for this key. Can be any string, integer or object.
   * @param string $form_id
   *   The form_id to get data for, will use the current form if not set.
   */
  protected function setDataValue($key, $value, $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();
    $data = $this->getTempData();
    NestedArray::setValue($data, (array) $key, $value, TRUE);
    $this->setTempData($data);

    $message = 'Data item %item has been set for user %user from the temporary storage %key';
    $replacements = [
      '%user' => $this->currentUser->getUsername(),
      '%key' => $this->getFormKey(),
      '%item' => $key,
    ];
    $this->getLogger($this->getLoggerChannel())->debug($message, $replacements);
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
  protected function getTempData($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $data = $this->store->get($this->getFormKey($form_id));

    $this->getLogger($this->getLoggerChannel())->debug('Data has been retrieved for user %user from the temporary storage %key', ['%user' => $this->currentUser->getUsername(), '%key' => $this->getFormKey()]);

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
  protected function setTempData(array $data, $form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    if (!$data || !is_array($data)) {
      $message = $this->t('Temporary data could not be saved for form %form_id', ['%form_id' => $form_id]);
      $this->getLogger($this->getLoggerChannel())->error($message);
      return;
    }

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $this->store->set($this->getFormKey($form_id), $data);

    $this->getLogger($this->getLoggerChannel())->debug('Data has been set for user %user from the temporary storage %key', ['%user' => $this->currentUser->getUsername(), '%key' => $this->getFormKey()]);
  }

  /**
   * Delete the temporary data for a form.
   *
   * @param string $form_id
   *   The form_id to set data for, will use the current form if not set.
   */
  protected function deleteTempData($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    $this->store->delete($this->getFormKey($form_id));

    $this->getLogger($this->getLoggerChannel())->debug('Data has been deleted for user %user from the temporary storage %key', ['%user' => $this->currentUser->getUsername(), '%key' => $this->getFormKey()]);
  }

  /**
   * Get all the data for the current flow state.
   */
  protected function getAllTempData() {
    $data = [];

    foreach ($this->getFlow()->getFlowForms() as $form) {
      $form_data = $this->getTempData($form);
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
      $form_data = $this->deleteTempData($form);
    }

    return $data;
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
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  protected function getLoggerChannel() {
    return 'par_forms';
  }

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName() {
    return isset($this->flow) ? $this->flow : '';
  }

  /**
   * Get the current flow entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The flow entity.
   */
  protected function getFlow() {
    return $this->getFlowStorage()->load($this->getFlowName());
  }

  /**
   * Get the injected Flow Entity Storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The flow storage handler.
   */
  protected function getFlowStorage() {
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
   * Get ignored form values.
   *
   * @return array
   *   An array representing additional key names to be removed from form data.
   */
  public function getIgnoredValues() {
    return isset($this->ignoreValues) ? (array) $this->ignoreValues : [];
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
   *   An ASCII-encoded cache ID that is at most 250 characters long.
   */
  public function normalizeKey($key) {
    $key = urlencode($key);
    // Nothing to do if the ID is a US ASCII string of 250 characters or less.
    $key_is_ascii = mb_check_encoding($key, 'ASCII');
    if (strlen($key) <= 240 && $key_is_ascii) {
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
    return substr($key, 0, 240 - strlen($hash)) . $hash;
  }

  /**
   * Start a manual session for anonymous users.
   */
  public function startAnonymousSession() {
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['session_started'])) {
      $_SESSION['session_started'] = TRUE;
      $this->sessionManager->start();
    }
  }

}
