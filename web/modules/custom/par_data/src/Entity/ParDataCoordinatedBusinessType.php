<?php

namespace Drupal\par_data\Entity;

/**
 * Defines the par_data_coordinated_business_type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_coordinated_business_t",
 *   label = @Translation("PAR Coordinated Business Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_coordinated_business_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_coordinated_business",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_coordinated_business_type/{par_data_coordinated_business_t}",
 *     "edit-form" = "/admin/structure/par_data/par_data_coordinated_business_type/{par_data_coordinated_business_t}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_coordinated_business_type/{par_data_coordinated_business_t}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_coordinated_business_type"
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
class ParDataCoordinatedBusinessType extends ParDataType {

}
