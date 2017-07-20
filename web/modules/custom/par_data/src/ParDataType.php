<?php

namespace Drupal\par_data;

use Drupal\trance\TranceType;

/**
 * The base PAR entity type class.
 *
 */
abstract class ParDataType extends TranceType {

  /**
   * The additional configuration options for this entity.
   *
   * Note: Whether a field is 'required' will be dictated by the field storage.
   *
   * @var array
   */
  public $configuration;

  /**
   * Get the configuratino for a given element.
   *
   * @return mixed
   */
  public function getConfiguration($element) {
    return isset($this->configuration[$element]) ? $this->configuration[$element] : NULL;
  }

}
