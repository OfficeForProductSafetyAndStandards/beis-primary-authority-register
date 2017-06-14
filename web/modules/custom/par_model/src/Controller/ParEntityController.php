<?php

namespace Drupal\par_model\Controller;

use Drupal\trance\Controller\TranceController;
use Drupal\trance\TranceInterface;
use Drupal\trance\TranceTypeInterface;

/**
 * Returns responses for ParEntity routes.
 */
class ParEntityController extends TranceController {

  /**
   * Provides the par_entity submission form.
   *
   * @param \Drupal\trance\TranceTypeInterface $par_entity_type
   *   The par_entity type entity for the par_entity.
   *
   * @return array
   *   A par_entity submission form.
   */
  public function add(TranceTypeInterface $par_entity_type) {
    return parent::add($par_entity_type);

  }

  /**
   * Generates an overview table of older revisions of a par_entity.
   *
   * @param \Drupal\trance\TranceInterface $par_entity
   *   A par_entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(TranceInterface $par_entity) {
    return parent::revisionOverview($par_entity);
  }

}
