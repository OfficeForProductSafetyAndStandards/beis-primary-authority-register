<?php

namespace Drupal\par_flows;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Entity\ParFlowInterface;
use Drupal\user\Entity\User;
use Drupal\user\PrivateTempStoreFactory;

class ParFlowDataHandler implements ParFlowDataHandlerInterface {

  const TEMP_PREFIX = 't:';
  const PERM_PREFIX = 'p:';

  /**
   * The PAR Flow Negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  protected $negotiator;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The private temporary storage for persisting multi-step form data.
   *
   * Each key (form) will last 1 week since it was last updated.
   *
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * The data parameters loaded from the route.
   *
   * Enables route variables to be fetched, but also overridden
   * by the implementing form/controller.
   *
   * @var array
   */
  protected $parameters = [];

  /**
   * Caches data loaded from the permanent store.
   *
   * @var array
   */
  protected $data = [];

  /**
   * Constructs a ParFlowDataHandler instance.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   *   The private temporary store.
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The entity bundle info service.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, ParFlowNegotiatorInterface $negotiator, ParDataManagerInterface $par_data_manager, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->store = $temp_store_factory;
    $this->negotiator = $negotiator;
    $this->parDataManager = $par_data_manager;
    $this->sessionManager = $session_manager;
    $this->account = $current_user;

    $this->parameters = $this->negotiator->getRoute()->getParameters();
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * Get's the current user account.
   *
   * @return \Drupal\Core\Session\AccountInterface|null
   */
  public function getCurrentUser() {
    return $this->account;
  }

  /**

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
    $data = $this->store->get(self::TEMP_PREFIX . $this->getFormKey($form_id));

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
    $this->store->set(self::TEMP_PREFIX . $this->getFormKey($form_id), $data);
  }

  /**
   * Delete the temporary data for a form.
   *
   * @param string $form_id
   *   The form_id to set data for, will use the current form if not set.
   */
  protected function deleteFormTempData($form_id = NULL) {
    $form_id = !empty($form_id) ? $form_id : $this->getFormId();

    $this->store->delete(self::TEMP_PREFIX . $this->getFormKey($form_id));
  }

  /**
   * Get all the data for the current flow state.
   */
  protected function getAllTempData() {
    $data = [];

    foreach ($this->negotiator->getFlow()->getFlowForms() as $form) {
      $form_data = $this->getFormTempData($form);
      $data = array_merge($data, $form_data);
    }

    return $data;
  }

  /**
   * Delete all the data for the current flow state.
   */
  protected function deleteStore() {
    $data = [];

    foreach ($this->negotiator->getFlow()->getFlowForms() as $form) {
      $form_data = $this->deleteFormTempData($form);
    }

    return $data;
  }

  public function getParameter($parameter) {
    return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : NULL;
  }
  public function getParameters() {
    return $this->parameters;
  }

  public function setParameter($parameter, $value) {
    $this->parameters[$parameter] = $value;
  }

  public function setParameters($params) {
    $this->parameters = $params;
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
  protected function getFormPermValue($key) {
    return NestedArray::getValue($this->data, (array) $key);
  }

  /**
   * Set a saved value into the local cache.
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
  protected function setFormPermValue($key, $value) {
    NestedArray::setValue($this->data, (array) $key, $value, TRUE);
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
