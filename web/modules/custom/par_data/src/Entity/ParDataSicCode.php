<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_sic_code entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_sic_code",
 *   label = @Translation("PAR SIC Code"),
 *   label_collection = @Translation("PAR SIC Codes"),
 *   label_singular = @Translation("PAR SIC Code"),
 *   label_plural = @Translation("PAR SIC Codes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count SIC code",
 *     plural = "@count SIC codes"
 *   ),
 *   bundle_label = @Translation("PAR SIC Code type"),
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
 *   base_table = "par_sic_codes",
 *   data_table = "par_sic_codes_field_data",
 *   revision_table = "par_sic_codes_revision",
 *   revision_data_table = "par_sic_codes_field_revision",
 *   admin_permission = "administer par_data_sic_code entities",
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
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "collection" = "/admin/content/par_data/par_data_sic_code",
 *     "canonical" = "/admin/content/par_data/par_data_sic_code/{par_data_sic_code}",
 *     "edit-form" = "/admin/content/par_data/par_data_sic_code/{par_data_sic_code}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_sic_code/{par_data_sic_code}/delete"
 *   },
 *   bundle_entity_type = "par_data_sic_code_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_sic_code_type.edit_form"
 * )
 */
class ParDataSicCode extends ParDataEntity {

  /**
   * Get the code.
   *
   * @return string
   */
  public function getCode() {
    return $this->get('sic_code')->getString();
  }

  /**
   * Get the code.
   *
   * @return string
   */
  public function getDescription() {
    return $this->get('description')->getString();
  }

  /**
   * Allows all relationships to be skipped.
   */
  public function lookupReferencesByAction($action = NULL) {
    switch ($action) {
      case 'manage':
        // Regulatory functions are treated as non-membership entities and therefore
        // their references are irrelevant when performing membership lookups.
        return FALSE;

    }

    return parent::lookupReferencesByAction($action);
  }

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // No relationships should be followed, this is one of the lowest tier entities.
        return FALSE;

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // SIC Code.
    $fields['sic_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('SIC Code'))
//      @TODO Make this field unique when migration to real SICs is complete.
//      ->addConstraint('UniqueField')
      ->setDescription(t('The SIC code identification number.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Description.
    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setDescription(t('The human readable description for the SIC code.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
