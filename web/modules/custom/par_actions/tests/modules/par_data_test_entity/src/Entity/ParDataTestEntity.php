<?php

namespace Drupal\par_data_test_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\Entity\ParDataEntity;

/**
 * Defines the par_data_sic_code entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_test_entity",
 *   label = @Translation("PAR Test Entity"),
 *   label_collection = @Translation("PAR Test Entities"),
 *   label_singular = @Translation("PAR Test Entity"),
 *   label_plural = @Translation("PAR Test Entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count test entity",
 *     plural = "@count test entities"
 *   ),
 *   bundle_label = @Translation("PAR Test Entity Type"),
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
 *   base_table = "par_test_entities",
 *   data_table = "par_test_entities_field_data",
 *   revision_table = "par_test_entities_revision",
 *   revision_data_table = "par_test_entities_field_revision",
 *   admin_permission = "administer par_data_test_entity entities",
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
 *     "collection" = "/admin/content/par_data/par_data_test_entity",
 *     "canonical" = "/admin/content/par_data/par_data_test_entity/{par_data_test_entity}",
 *     "edit-form" = "/admin/content/par_data/par_data_test_entity/{par_data_test_entity}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_test_entity/{par_data_test_entity}/delete"
 *   },
 *   bundle_entity_type = "par_data_test_entity_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_test_entity_type.edit_form"
 * )
 */
class ParDataTestEntity extends ParDataEntity {

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

    // Test entity expiry date.
    $fields['expiry_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Expiry Date'))
      ->setDescription(t('The expiry date.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 4,
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
