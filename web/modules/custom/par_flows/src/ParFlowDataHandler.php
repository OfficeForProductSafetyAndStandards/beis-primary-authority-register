<?php

namespace Drupal\par_flows;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_forms\ParFormPluginInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class ParFlowDataHandler implements ParFlowDataHandlerInterface {

  const TEMP_PREFIX = 't:';
  const PERM_PREFIX = 'p:';
  const META_PREFIX = 'm:';

  const ENTRY_POINT = 'entry_point';

  /**
   * The private temporary storage for persisting multistep form data.
   *
   * Each key (form) will last 1 week since it was last updated.
   *
   * @var PrivateTempStore $store
   */
  protected PrivateTempStore $store;

  /**
   * The data parameters loaded from the route.
   *
   * Enables route variables to be fetched, but also overridden
   * by the implementing form/controller.
   *
   * @var array|ParameterBag $parameters
   */
  protected iterable $parameters = [];

  /**
   * The raw data parameters.
   *
   * @var array|ParameterBag $parameters
   */
  protected iterable $rawParameters = [];

  /**
   * Caches data loaded from the permanent store.
   *
   * @var array $data
   */
  protected array $data = [];

  /**
   * Constructs a ParFlowDataHandler instance.
   *
   * @param PrivateTempStoreFactory $temp_store_factory
   *   The private temporary store.
   * @param ParFlowNegotiatorInterface $negotiator
   *   The flow negotiator.
   * @param ParDataManagerInterface $parDataManager
   *   The par data manager.
   * @param SessionManagerInterface $sessionManager
   *   The session manager.
   * @param AccountInterface $account
   *   The entity bundle info service.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, /**
   * The PAR Flow Negotiator.
   */
  public ParFlowNegotiatorInterface $negotiator, /**
   * The PAR data manager for acting upon PAR Data.
   */
  protected ParDataManagerInterface $parDataManager, /**
   * The PAR data manager for acting upon PAR Data.
   */
  protected SessionManagerInterface $sessionManager, /**
   * The current user account.
   */
  protected AccountInterface $account) {
    $this->store = $temp_store_factory->get('par_flows_flows');

    $this->reset();
  }

  /**
   * Reset the data handler.
   */
  public function reset() {
    // The data parameters are set based on the current route
    // but can be overridden when needed (such as access callbacks).
    $this->parameters = $this->negotiator->getRoute()->getParameters();
    $this->rawParameters = $this->negotiator->getRoute()->getRawParameters();
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
  #[\Override]
  public function getDefaultValues($key, $default = '', $cid = NULL) {
    $value = $this->getTempDataValue($key, $cid);

    if (!isset($value)) {
      $value = $this->getFormPermValue($key);
    }

    if (!isset($value)) {
      $value = $default;
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getTempDataValue($key, $cid = NULL) {
    $tmp_data = $this->getFormTempData($cid);

    $value = NestedArray::getValue($tmp_data, (array) $key, $exists);

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setTempDataValue($key, $value, $cid = NULL) {
    $data = $this->getFormTempData($cid);
    NestedArray::setValue($data, (array) $key, $value, TRUE);
    $this->setFormTempData($data, $cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
  public function setFormTempData(array $data, $cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    if (!$data || !is_array($data)) {
      return;
    }

    $this->store->set(self::TEMP_PREFIX . $cid, $data);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deleteFormTempData($cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    $this->store->delete(self::TEMP_PREFIX . $cid);
  }

  /**
   * Get the data submitted by a given plugin.
   *
   * If the plugin allows multiple values this data will be structured and the
   * resulting array will be an array of plugin data keyed by cardinality values.
   */
  public function getPluginTempData(ParFormPluginInterface $plugin, $cid = NULL) {
    // If the plugin data is flattened return all the form data,
    // otherwise return just the data found for the given plugin prefix.
    return $plugin->isFlattened() ?
      $this->getFormTempData($cid) :
      $this->getTempDataValue($plugin->getPrefix(), $cid);
  }

  /**
   * Set the data for a given plugin.
   *
   * If the plugin allows multiple values this data will be structured and the
   * resulting array will be an array of plugin data keyed by cardinality values.
   */
  public function setPluginTempData(ParFormPluginInterface $plugin, array $data, $cid = NULL) {
    // If the plugin data is flattened set all the form data,
    // otherwise set just the data found for the given plugin prefix.
    $plugin->isFlattened() ?
      $this->setFormTempData($data, $cid) :
      $this->setTempDataValue($plugin->getPrefix(), $data, $cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
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
  #[\Override]
  public function getMetaDataValue($key, $cid = NULL) {
    $meta_data = $this->getFlowMetaData($cid);
    $value = NestedArray::getValue($meta_data, (array) $key, $exists);
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setMetaDataValue($key, $value, $cid = NULL) {
    $meta_data = $this->getFlowMetaData($cid);
    NestedArray::setValue($meta_data, (array) $key, $value, TRUE);
    $this->setFlowMetaData($meta_data, $cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getFlowMetaData($cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowStateKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();
    $meta_data = $this->store->get(self::META_PREFIX . $cid);

    return !empty($meta_data) ? $meta_data : [];
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setFlowMetaData(array $meta_data, $cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowStateKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    if (!$meta_data || !is_array($meta_data)) {
      return;
    }

    $this->store->set(self::META_PREFIX . $cid, $meta_data);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deleteFlowMetaData($cid = NULL) {
    $cid = empty($cid) ? $this->negotiator->getFlowStateKey() : $cid;

    // Start an anonymous session if required.
    $this->startAnonymousSession();

    $this->store->delete(self::META_PREFIX . $cid);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function deleteStore() {
    $data = [];

    // Delete the temporary store data for each form.
    foreach ($this->negotiator->getFlow()->getFlowForms() as $form) {
      $cid = $this->negotiator->getFormKey($form);
      $this->deleteFormTempData($cid);
    }

    // Delete the metadata store for the flow.
    $this->deleteFlowMetaData($this->negotiator->getFlowStateKey());

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getParameter($parameter) {
    return $this->parameters->get($parameter);
  }

  /**
   * {@inheritdoc}
   */
  public function getRawParameter($parameter) {
    return $this->rawParameters->get($parameter);
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   */
  #[\Override]
  public function getParameters() {
    return $this->parameters->all();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   */
  public function getRawParameters() {
    return $this->rawParameters->all();
  }

  /**
   * {@inheritdoc}
   *
   * @param $parameter
   * @param $value
   */
  #[\Override]
  public function setParameter($parameter, $value) {
    $this->parameters->set($parameter, $value);

    // Set the raw parameter value, this will need cleansing if an entity was passed.
    // Note that the raw parameter cannot be set for arrays or any other non-scalar
    // values other due to lack of a transparent conversion method.
    if ($value instanceof EntityInterface) {
      $this->rawParameters->set($parameter, $value->id());
    }
    elseif (is_scalar($value)) {
      $this->rawParameters->set($parameter, $value);
    }
  }

  /**
   * {@inheritdoc}
   *
   * @param $params
   */
  #[\Override]
  public function setParameters($params) {
    foreach ($params as $key => $param) {
      $this->setParameter($key, $param);
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getFormPermValue($key, ParFormPluginInterface $plugin = NULL) {
    $key = (array) $key;

    // Allow the plugin namespace to be used as a prefix if a plugin is passed in.
    if ($plugin && $plugin instanceof ParFormPluginInterface) {
      array_unshift($key, $plugin->getPluginNamespace());
    }

    return NestedArray::getValue($this->data, $key);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function setFormPermValue($key, $value, ParFormPluginInterface $plugin = NULL) {
    $key = (array) $key;
    // Allow the plugin namespace to be used as a prefix if a plugin is passed in.
    if ($plugin && $plugin instanceof ParFormPluginInterface) {
      array_unshift($key, $plugin->getPluginNamespace());
    }

    NestedArray::setValue($this->data, $key, $value, TRUE);
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

  /**
   * Filters a nested array recursively.
   *
   * This is a copy of NestedArray::filter() with the changes made to filter
   * multidimensional arrays correctly.
   * @see https://www.drupal.org/project/drupal/issues/3381640
   *
   * @param array $array
   *   The filtered nested array.
   * @param ?callable $callable
   *   The callable to apply for filtering.
   *
   * @return array
   *   The filtered array.
   */
  public function filter(array $array, callable $callable = NULL) {
    foreach ($array as &$element) {
      if (is_array($element)) {
        $element = static::filter($element, $callable);
      }
    }

    // Filter the parents after the child elements.
    return is_callable($callable) ? array_filter($array, $callable) : array_filter($array);
  }

  /**
   * A custom method to filter submitted values.
   *
   * Values that should be treated as empty:
   * - NULL
   * - "" or empty strings
   *
   * Values that should not be treated as empty:
   * - False
   * - 0
   *
   *
   * @return bool
   */
  public static function filterValues(mixed $value): bool {
    // Exclude bool and numeric values from the filtering.
    return !empty($value) || is_numeric($value) || is_bool($value);
  }
}
