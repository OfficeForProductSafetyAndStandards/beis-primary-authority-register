<?php

namespace Drupal\par_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * The base form controller for all PAR forms.
 */
abstract class ParBaseForm extends FormBase {

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $flowStorage;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow;

  /**
   * @var string
   *   A machine safe value representing any states or combination of states that alter the form behaviour.
   *
   * e.g. A example of a state would be whether the flow is being created, edited or reviewed.
   */
  protected $state = 'default';

  /**
   * Constructs a \Drupal\par_forms\Form\ParBaseForm.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user, EntityStorageInterface $flow_storage) {
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
   */
  protected function getNextStep() {
    $flow = $this->getFlow();
    // Lookup the current step to more accurately determine the next step.
    $current_step = $flow->getStepByFormId($this->getFormId());
    $next_step = isset($current_step['step']) ? $flow->getStep(++$current_step['step']) : $flow->getStep(1);

    // If there is no next step we'll stay on this step.
    return isset($next_step['route']) ? $next_step['route'] : $current_step['route'];
  }

  /**
   * Wraps call to static NestedArray::getValue to make more testable.
   *
   * @param string|array $key
   * @param mixed $default
   *
   * @return mixed|null
   */
  public function &getDataValue($key, $default = '') {
    $exists = NULL;
    $data = $this->getTempData();
    $value = &NestedArray::getValue($data, (array) $key, $exists);
    if (!$exists) {
      $value = $default;
    }

    $this->getLogger($this->getLoggerChannel())->debug('Data item %item has been retrieved for user %user from the temporary storage %key', ['%user' => $this->currentUser->getUsername(), '%key' => $this->getFormKey(), '%item' => $key]);

    return $value;
  }

  /**
   * Wraps call to static NestedArray::setValue to make more testable.
   *
   * @param $key
   * @param $value
   */
  public function setDataValue($key, $value) {
    $data = $this->getTempData();
    NestedArray::setValue($data, (array) $key, $value, TRUE);
    $this->setTempData($data);

    $this->getLogger($this->getLoggerChannel())->debug('Data item %item has been set for user %user from the temporary storage %key', ['%user' => $this->currentUser->getUsername(), '%key' => $this->getFormKey(), '%item' => $key]);
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
  protected function setTempData($data, $form_id = NULL) {
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
   * Delete the temporary data for a form
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
   */
  public function getState() {
    return isset($this->state) ? $this->state : '';
  }

  /**
   * Get the form Key.
   *
   * @param null $form_id
   * @return string
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
      $_SESSION['session_started'] = true;
      $this->sessionManager->start();
    }
  }
}
