<?php

namespace Drupal\par_forms\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the PAR Form Flow entity.
 *
 * @ConfigEntityType(
 *   id = "par_form_flow",
 *   label = @Translation("PAR Form Flow"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_forms",
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
 *     "description"
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
   * Get the description for this flow.
   */
  public function getDescription() {
    return $this->description;
  }

}
