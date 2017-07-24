<?php

namespace Drupal\par_data\Entity;

use Drupal\par_data\ParDataType;

/**
 * Defines the par_data_enforcement_notice_type entity.
 *
 * @ConfigEntityType(
 *   id = "par_data_enforcement_notice_type",
 *   label = @Translation("PAR Enforcement Notice Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\trance\TranceTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\trance\Form\TranceTypeForm",
 *       "edit" = "Drupal\trance\Form\TranceTypeForm",
 *       "delete" = "Drupal\trance\Form\TranceTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "par_data_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "par_data_authority",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/par_data/par_data_enforcement_notice_type/{par_data_enforcement_notice_type}",
 *     "edit-form" = "/admin/structure/par_data/par_data_enforcement_notice_type/{par_data_enforcement_notice_type}/edit",
 *     "delete-form" = "/admin/structure/par_data/par_data_enforcement_notice_type/{par_data_enforcement_notice_type}/delete",
 *     "collection" = "/admin/structure/par_data/par_data_enforcement_notice_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "help",
 *     "configuration"
 *   }
 * )
 */
class ParDataEnforcementNoticeType extends ParDataType {

}
