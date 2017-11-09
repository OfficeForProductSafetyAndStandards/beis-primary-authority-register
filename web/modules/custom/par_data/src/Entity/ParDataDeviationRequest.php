<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_deviation_request entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_deviation_request",
 *   label = @Translation("PAR Deviation Request"),
 *   label_collection = @Translation("PAR Deviation Requests"),
 *   label_singular = @Translation("PAR Deviation Request"),
 *   label_plural = @Translation("PAR Deviation Requests"),
 *   label_count = @PluralTranslation(
 *     singular = "@count deviation request",
 *     plural = "@count deviation requests"
 *   ),
 *   bundle_label = @Translation("PAR Deviation Request type"),
 *   handlers = {
 *     "storage" = "Drupal\par_data\ParDataStorage",
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
 *   base_table = "par_inspection_plan_deviation_requests",
 *   data_table = "par_inspection_plan_deviation_requests_field_data",
 *   revision_table = "par_inspection_plan_deviation_requests_revision",
 *   revision_data_table = "par_inspection_plan_deviation_requests_field_revision",
 *   admin_permission = "administer par_data_deviation_request entities",
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
 *     "collection" = "/admin/content/par_data/par_data_deviation_request",
 *     "canonical" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}",
 *     "edit-form" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}/delete"
 *   },
 *   bundle_entity_type = "par_data_deviation_request_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_deviation_request_t.edit_form"
 * )
 */
class ParDataDeviationRequest extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Request Date.
    $fields['request_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Request Date'))
      ->setDescription(t('The date this deviation request was issued.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Notes.
    $fields['notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Notes'))
      ->setDescription(t('Notes about this deviation request.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Primary Authority status.
    $fields['primary_authority_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Primary Authority Status'))
      ->setDescription(t('The status of the primary authority on this request.'))
      ->setRequired(TRUE)
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // PA notes.
    $fields['primary_authority_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Primary Authority Notes'))
      ->setDescription(t('Notes about this deviation request from the primary authority.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 6,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
