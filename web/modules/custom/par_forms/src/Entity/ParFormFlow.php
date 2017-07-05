<?php

namespace Drupal\par_forms\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_form_flow",
 *   label = @Translation("PAR Form Flow"),
 *   config_prefix = "par_form_flow",
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\Core\Entity\EntityForm",
 *       "edit" = "Drupal\Core\Entity\EntityForm",
 *       "delete" = "Drupal\Core\Entity\EntityConfirmFormBase"
 *     }
 *   },
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/par/form-flow/{par_entity_type}",
 *     "edit-form" = "/admin/config/par/form-flow/{par_entity_type}/edit",
 *     "delete-form" = "/admin/config/par/form-flow/{par_entity_type}/delete",
 *     "collection" = "/admin/config/par/form-flow"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "steps"
 *   }
 * )
 */
class ParFormFlow extends ConfigEntityBase {

  /**
   * The flow ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The flow label.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this flow.
   *
   * @var string
   */
  protected $description;

  /**
   * The steps for this flow.
   *
   * @var array
   */
  protected $steps;

  /**
   * The caches the routes for quicker lookup.
   *
   * @var array
   */
  protected $routeCache;

  /**
   * Get the description for this flow.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Get all steps in this flow.
   */
  public function getSteps() {
    return $this->steps ?: [];
  }

  /**
   * Get a step by it's index.
   *
   * @param int $index
   *   The step number that is required.
   *
   * @return array
   *   An array with values for the form_id & route
   */
  public function getStep($index) {
    return isset($this->steps[$index]) ? $this->steps[$index] : NULL;
  }

  /**
   * Get a step by.
   *
   * @param int $index
   *   The step number that is required.
   *
   * @return array
   *   An array with values for the form_id & route
   */
  public function getStepByFormId($form_id) {
    foreach ($this->getSteps() as $key => $step) {
      if (isset($step['form_id']) && $form_id === $step['form_id']) {
        $match = [
          'step' => $key
        ] + $step;
      }
    }
    return isset($match) ? $match : [];
  }

  /**
   * Get all the forms in a given flow.
   *
   * @return array
   *   An array of strings representing form IDs.
   */
  public function getFlowForms() {
    $forms = [];

    foreach ($this->getSteps() as $step) {
      if (isset($step['form_id'])) {
        $forms[] = (string) $step['form_id'];
      }
    }

    return $forms;
  }

}
