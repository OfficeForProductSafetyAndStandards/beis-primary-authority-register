<?php

namespace Drupal\par_data_test_entity\Entity;

use Drupal\par_data\Entity\ParDataType;

/**
 * Defines the par_data_test_entity_type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_test_entity_type",
 *   label = @Translation("PAR Test Entity Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_test_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_test_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_test_entity_type/{par_data_test_entity_type}",
 *     "edit-form" = "/admin/structure/par_data/par_data_test_entity_type/{par_data_test_entity_type}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_test_entity_type/{par_data_test_entity_type}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_test_entity_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "help",
 *     "isDeletable",
 *     "isRevokable",
 *     "isArchivable",
 *     "configuration"
 *   }
 * )
 */
class ParDataTestEntityType extends ParDataType {

}
