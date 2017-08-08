<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceType;

/**
 * The base PAR entity type class.
 *
 */
abstract class ParDataType extends TranceType implements ParDataTypeInterface {

  /**
   * The additional configuration options for this entity.
   *
   * Note: Whether a field is 'required' will be dictated by the field storage.
   *
   * @var array
   */
  public $configuration;

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return !empty($this->configuration) ? $this->configuration : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationElement($element) {
    $config = $this->getConfiguration();
    return isset($config[$element]) ? $config[$element] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationByType($type) {
    $elements = [];
    foreach ($this->getConfiguration() as $element => $configurations) {
      if ($config = $this->getConfigurationElementByType($element, $type)) {
        $elements[$element] = $config;
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationElementByType($element, $type) {
    $element_configuration = $this->getConfigurationElement($element);
    return isset($element_configuration[$type]) ? $element_configuration[$type] : NULL;
  }

}
