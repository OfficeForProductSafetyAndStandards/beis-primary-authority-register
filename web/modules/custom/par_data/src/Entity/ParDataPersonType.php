<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceType;

/**
 * Defines the par_data_person type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_person_type",
 *   label = @Translation("PAR Person Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_person_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_person",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_person/{par_data_person}",
 *     "edit-form" = "/admin/structure/par_data/par_data_person/{par_data_person}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_person/{par_data_person}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_person"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "help"
 *   }
 * )
 */
class ParDataPersonType extends TranceType {

}
