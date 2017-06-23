<?php

namespace Drupal\par_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * @var \Drupal\Core\Entity\EntityStorageInterface
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
   * @return string
   *   Get the logger channel to use.
   */
  protected function getLoggerChannel() {
    return 'par_forms';
  }

  /**
   * @return string
   *   The name of the flow.
   */
  protected function getFlow() {
    return $this->flowStorage->load($this->flow);
  }

  /**
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The flow storage handler.
   */
  protected function getFlowStorage() {
    return $this->flowStorage;
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
    $this->setTempData($form_state);
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
   * Retrieve the temporary data for this form.
   *
   * @return NULL|array
   *   The values stored in the temp store.
   */
  protected function getTempData() {
    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $values = $this->store->get($this->getFormKey());
    return $values ?: NULL;
  }

  /**
   * Retrieve the temporary data for this form.
   *
   * @param FormStateInterface $data
   *   The form state object that needs to be saved.
   */
  protected function setTempData(FormStateInterface $data) {
    if (!$data instanceof FormStateInterface) {
      $message = $this->t('Temporary data could not be saved for form %form_id', ['%form_id' => $this->getFormId()]);
      $this->getLogger($this->getLoggerChannel())->error($message);
      return;
    }

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $this->store->set($this->getFormKey(), $data->getValues());
  }

  /**
   * Get the form Key.
   *
   * @param null $form_id
   * @return string
   */
  protected function getFormKey($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();
    $key = implode('_', [$this->flow, $this->state, $form_id]);
    // @TODO work out if Key/Value store normalizes keys itself, if so, remove.
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
  protected function normalizeKey($key) {
    $key = urlencode($key);
    // Nothing to do if the ID is a US ASCII string of 250 characters or less.
    $key_is_ascii = mb_check_encoding($key, 'ASCII');
    if (strlen($key) <= 250 && $key_is_ascii) {
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
    return substr($key, 0, 250 - strlen($hash)) . $hash;
  }

  /**
   * Start a manual session for anonymous users.
   */
  protected function startAnonymousSession() {
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['session_started'])) {
      $_SESSION['session_started'] = true;
      $this->sessionManager->start();
    }
  }
}
