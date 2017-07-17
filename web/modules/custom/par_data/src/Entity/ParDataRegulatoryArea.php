<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;

/**
 * Defines the par_data_regulatory_area entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_regulatory_area",
 *   label = @Translation("PAR Regulatory Area"),
 *   label_collection = @Translation("PAR Regulatory Areas"),
 *   label_singular = @Translation("PAR Regulatory Area"),
 *   label_plural = @Translation("PAR Regulatory Areas"),
 *   label_count = @PluralTranslation(
 *     singular = "@count regulatory area",
 *     plural = "@count regulatory areas"
 *   ),
 *   bundle_label = @Translation("PAR Regulatory Area type"),
 *   handlers = {
 *     "storage" = "Drupal\trance\TranceStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\trance\TranceViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\trance\Access\TranceAccessControlHandler",
 *   },
 *   base_table = "par_data_regulatory_area",
 *   data_table = "par_data_regulatory_area_field_data",
 *   revision_table = "par_data_regulatory_area_revision",
 *   revision_data_table = "par_data_regulatory_area_field_revision",
 *   admin_permission = "administer par_data_regulatory_area entities",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status"
 *   },
 *   links = {
 *     "collection" = "/admin/content/par_data/par_data_regulatory_area",
 *     "canonical" = "/admin/content/par_data/par_data_regulatory_area/{par_entity}",
 *     "edit-form" = "/admin/content/par_data/par_data_regulatory_area/{par_entity}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_regulatory_area/{par_entity}/delete"
 *   },
 *   bundle_entity_type = "par_data_regulatory_area_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_regulatory_area_type.edit_form"
 * )
 */
class ParDataRegulatoryArea extends Trance {

}
