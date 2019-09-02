<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityTypeInterface;

/**
* Interface for the Par Flow Data Handler.
*/
interface ParFlowDataHandlerInterface {

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
   * @param string $cid
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return mixed|null
   *   The value for this key.
   */
  public function getDefaultValues($key, $default = '', $cid = NULL);

  /**
   * Get a value from the temp data store for a form element.
   *
   * @param string|array $key
   *   The key to search for.
   * @param string $cid
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return mixed|null
   *   The value for this key.
   */
  public function getTempDataValue($key, $cid = NULL);

  /**
   * Set a value for a form element in the temporary store.
   *
   * @param string $key
   *   The key to search for.
   * @param mixed $value
   *   The value to store for this key. Can be any string, integer or object.
   * @param string $cid
   *   The cache id to get data for, will use the current form if not set.
   */
  public function setTempDataValue($key, $value, $cid = NULL);

  /**
   * Retrieve the temporary data for a form.
   *
   * @param string $cid
   *   The cache id to get data for, will use the current form if not set.
   *
   * @return array
   *   The values stored in the temp store.
   */
  public function getFormTempData($cid = NULL);

  /**
   * Retrieve the temporary data for a form.
   *
   * @param array $data
   *   The array of data to be saved.
   * @param string $cid
   *   The cache id to set data for, will use the current form if not set.
   */
  public function setFormTempData(array $data, $cid = NULL);

  /**
   * Delete the temporary data for a form.
   *
   * @param string $cid
   *   The cache id to delete data for, will use the current form if not set.
   */
  public function deleteFormTempData($cid = NULL);

  /**
   * Get all the temporary data for the current flow state.
   */
  public function getAllTempData();

  /**
   * Gets a meta data value for the flow.
   *
   * These values can be generic to the whole flow (by default), or a
   * flow key specific to a given step in the flow can be passed in to
   * get meta data for that particular step.
   *
   * @param string|array $key
   *   The key to search for.
   * @param string $cid
   *   The form_id to get data for, will use the current form if not set.
   *
   * @return mixed|null
   *   The value for this key.
   */
  public function getMetaDataValue($key, $cid = NULL);

  /**
   * Set a meta value for the flow.
   *
   * @see self::getMetaDataValue();
   *
   * @param string $key
   *   The key to search for.
   * @param mixed $value
   *   The value to store for this key. Can be any string, integer or object.
   * @param string $cid
   *   The cache id to get data for, will use the current form if not set.
   */
  public function setMetaDataValue($key, $value, $cid = NULL);

  /**
   * Retrieve the meta data for a flow.
   *
   * The meta data can be generic to the whole flow (by default), or a
   * flow key specific to a given step in the flow can be passed in to
   * get meta data for that particular step.
   *
   * @param string $cid
   *   The cache id to get data for, will use the current form if not set.
   *
   * @return array
   *   The values stored in the temp meta store.
   */
  public function getFlowMetaData($cid = NULL);

  /**
   * Set the meta data for a flow.
   *
   * @see self::getFlowMetaData();
   *
   * @param array $data
   *   The array of data to be saved.
   * @param string $cid
   *   The cache id to set data for, will use the current form if not set.
   */
  public function setFlowMetaData(array $data, $cid = NULL);

  /**
   * Delete the meta data for a flow.
   *
   * @param string $cid
   *   The cache id to delete data for, will use the current flow if not set.
   */
  public function deleteFlowMetaData($cid = NULL);

  /**
   * Delete all the data for the current flow state.
   */
  public function deleteStore();

  public function getParameter($parameter);

  public function getParameters();

  public function setParameter($parameter, $value);

  public function setParameters($params);

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
  public function getFormPermValue($key);

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
  public function setFormPermValue($key, $value);
}
