<?php

namespace Drupal\par_data\Entity;

/**
 * Defines the par_data_partnership_le_type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_partnership_le_type",
 *   label = @Translation("PAR Partnership Legal Entity Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_partnership_legal_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_partnership_le",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_partnership_legal_entity_type/{par_data_partnership_legal_entity_type}",
 *     "edit-form" = "/admin/structure/par_data/par_data_partnership_legal_entity_type/{par_data_partnership_legal_entity_type}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_partnership_legal_entity_type/{par_data_partnership_legal_entity_type}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_partnership_legal_entity_type"
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
class ParDataPartnershipLegalEntityType extends ParDataType {

}
