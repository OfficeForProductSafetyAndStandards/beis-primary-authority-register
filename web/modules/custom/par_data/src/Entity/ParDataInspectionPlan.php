<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

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
 *     "views_data" = "Drupal\par_data\Views\ParDataViewsData",
 *     "form" = {
 *       "default" = "Drupal\par_data\Form\ParDataForm",
 *       "add" = "Drupal\par_data\Form\ParDataForm",
 *       "edit" = "Drupal\par_data\Form\ParDataForm",
 *       "delete" = "Drupal\par_data\Form\ParDataDeleteForm",
 *     },
 *     "access" = "Drupal\par_data\Access\ParDataAccessControlHandler",
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
class ParDataInspectionPlan extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Valid Date.
    $fields['valid_date'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Valid Date'))
      ->setDescription(t('The date range this inspection plan is valid for.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Approved RD Executive.
    $fields['approved_rd_executive'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Approved by RD Executive'))
      ->setDescription(t('Whether this inspection plan has been approved by an RD executive.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Approved RD Executive.
    $fields['consulted_national_regulator'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('National Regulator Consulted'))
      ->setDescription(t('Whether the national regulator has been consulted about this inspection plan.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Documents.
    $fields['document'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document'))
      ->setDescription(t('Documents relating to the inspection.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/advice',
      ])
      ->setDisplayOptions('form', [
        'weight' => 4,
        'default_widget' => "file_generic",
        'default_formatter' => "file_default",
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Inspection Status.
    // {@depreciated} We will use the concept of workflow states going forward.
    $fields['inspection_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('(Depreciated) Inspection Status'))
      ->setDescription(t('The current status of the inspection plan itself. For example, current, expired, replaced.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Revoked.
    $fields['revoked'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revoked'))
      ->setDescription(t('Whether the inspection plan has been revoked.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
