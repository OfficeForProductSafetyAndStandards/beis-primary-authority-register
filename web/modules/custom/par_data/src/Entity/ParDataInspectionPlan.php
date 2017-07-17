<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the par_data_inspection_plan entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_inspection_plan",
 *   label = @Translation("PAR Inspection Plan"),
 *   label_collection = @Translation("PAR Inspection Plans"),
 *   label_singular = @Translation("PAR Inspection Plan"),
 *   label_plural = @Translation("PAR Inspection Plans"),
 *   label_count = @PluralTranslation(
 *     singular = "@count inspection plan",
 *     plural = "@count inspection plans"
 *   ),
 *   bundle_label = @Translation("PAR Inspection Plan type"),
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
 *   base_table = "par_inspection_plans",
 *   data_table = "par_inspection_plans_field_data",
 *   revision_table = "par_inspection_plans_revision",
 *   revision_data_table = "par_inspection_plans_field_revision",
 *   admin_permission = "administer par_data_inspection_plan entities",
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
 *     "collection" = "/admin/content/par_data/par_data_inspection_plan",
 *     "canonical" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}",
 *     "edit-form" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}/delete"
 *   },
 *   bundle_entity_type = "par_data_inspection_plan_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_inspection_plan_type.edit_form"
 * )
 */
class ParDataInspectionPlan extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    return $fields;
  }

}
