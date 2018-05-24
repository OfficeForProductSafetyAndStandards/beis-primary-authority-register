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
    $this->store = $temp_store_factory->get('par_flows_flows');
    $this->negotiator = $negotiator;
    $this->parDataManager = $par_data_manager;
    $this->sessionManager = $session_manager;
    $this->account = $current_user;

    $this->reset();
  }

  /**
   * Reset the data handler.
   */
  public function reset() {
    // The data parameters are set based on the current route
    // but can be overridden when needed (such as access callbacks).
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
   * {@inheritdoc}
   */
  public function getDefaultValues($key, $default = '', $cid = NULL) {
    $value = $this->getTempDataValue($key, $cid);

    if (!$value) {
      $value = $this->getFormPermValue($key);
    }

    if (!$value) {
      $value = $default;
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getTempDataValue($key, $cid = NULL) {
    $tmp_data = $this->getFormTempData($cid);

    $value = NestedArray::getValue($tmp_data, (array) $key, $exists);

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTempDataValue($key, $value, $cid = NULL) {
    $data = $this->getFormTempData($cid);
    NestedArray::setValue($data, (array) $key, $value, TRUE);
    $this->setFormTempData($data, $cid);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormTempData($cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $data = $this->store->get(self::TEMP_PREFIX . $cid);

    return !empty($data) ? $data : [];
  }

  /**
   * {@inheritdoc}
   */
  public function setFormTempData(array $data, $cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    if (!$data || !is_array($data)) {
      return;
    }

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $this->store->set(self::TEMP_PREFIX . $cid, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFormTempData($cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    $this->store->delete(self::TEMP_PREFIX . $cid);
  }

  /**
   * {@inheritdoc}
   */
  public function getAllTempData() {
    $data = [];

    foreach ($this->negotiator->getFlow()->getFlowForms() as $form) {
      $cid = $this->negotiator->getFormKey($form);
      $form_data = $this->getFormTempData($cid);
      $data = array_merge($data, $form_data);
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteStore() {
    $data = [];

    foreach ($this->negotiator->getFlow()->getFlowForms() as $form) {
      $cid = $this->negotiator->getFormKey($form);
      $this->deleteFormTempData($cid);
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getParameter($parameter) {
    return $this->parameters->get($parameter);
  }

  /**
   * {@inheritdoc}
   *
   * @return array|\Symfony\Component\HttpFoundation\ParameterBag
   */
  public function getParameters() {
    return $this->parameters->all();
  }

  /**
   * {@inheritdoc}
   *
   * @param $parameter
   * @param $value
   */
  public function setParameter($parameter, $value) {
    $this->parameters->set($parameter, $value);
  }

  /**
   * {@inheritdoc}
   *
   * @param $params
   */
  public function setParameters($params) {
    foreach ($params as $key => $param) {
      $this->setParameter($key, $param);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormPermValue($key) {
    return NestedArray::getValue($this->data, (array) $key);
  }

  /**
   * {@inheritdoc}
   */
  public function setFormPermValue($key, $value) {
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
