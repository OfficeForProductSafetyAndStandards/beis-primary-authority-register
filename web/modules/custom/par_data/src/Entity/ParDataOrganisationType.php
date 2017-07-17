<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceType;

/**
 * Defines the par_entity type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_organisation_type",
 *   label = @Translation("PAR Organisation Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_organisation_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_organisation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_organisation/{par_data_organisation}",
 *     "edit-form" = "/admin/structure/par_data/par_data_organisation/{par_data_organisation}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_organisation/{par_data_organisation}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_organisation"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "help"
 *   }
 * )
 */
class ParDataOrganisationType extends TranceType {

}
