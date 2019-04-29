<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_regulatory_function entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_regulatory_function",
 *   label = @Translation("PAR Regulatory Function"),
 *   label_collection = @Translation("PAR Regulatory Functions"),
 *   label_singular = @Translation("PAR Regulatory Function"),
 *   label_plural = @Translation("PAR Regulatory Functions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count regulatory function",
 *     plural = "@count regulatory functions"
 *   ),
 *   bundle_label = @Translation("PAR Regulatory Function type"),
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
 *   base_table = "par_regulatory_functions",
 *   data_table = "par_regulatory_functions_field_data",
 *   revision_table = "par_regulatory_functions_revision",
 *   revision_data_table = "par_regulatory_functions_field_revision",
 *   admin_permission = "administer par_data_regulatory_function entities",
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
 *     "collection" = "/admin/content/par_data/par_data_regulatory_function",
 *     "canonical" = "/admin/content/par_data/par_data_regulatory_function/{par_data_regulatory_function}",
 *     "edit-form" = "/admin/content/par_data/par_data_regulatory_function/{par_data_regulatory_function}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_regulatory_function/{par_data_regulatory_function}/delete"
 *   },
 *   bundle_entity_type = "par_data_regulatory_function_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_regulatory_function_t.edit_form"
 * )
 */
class ParDataRegulatoryFunction extends ParDataEntity {

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

    // Name.
    $fields['function_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Regulatory Function Name'))
      ->setDescription(t('The name of the regulatory function.'))
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

    return $fields;
  }

}
