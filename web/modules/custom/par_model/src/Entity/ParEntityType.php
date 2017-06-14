<?php

namespace Drupal\par_model\Entity;

use Drupal\trance\TranceType;

/**
 * Defines the par_entity type entity.
 *
 * @ConfigEntityType(
 *   id = "par_entity_type",
 *   label = @Translation("par_entity type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_entity_type/{par_entity_type}",
 *     "edit-form" = "/admin/structure/par_entity_type/{par_entity_type}/edit",
 *     "delete-form" = "/admin/structure/par_entity_type/{par_entity_type}/delete",
 *     "collection" = "/admin/structure/par_entity_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "help"
 *   }
 * )
 */
class ParEntityType extends TranceType {

}
