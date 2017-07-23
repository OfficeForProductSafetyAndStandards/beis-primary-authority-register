<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_legal_entity entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_legal_entity",
 *   label = @Translation("PAR Legal Entity"),
 *   label_collection = @Translation("PAR Legal Entities"),
 *   label_singular = @Translation("PAR Legal Entity"),
 *   label_plural = @Translation("PAR Legal Entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count legal entity",
 *     plural = "@count legal entities"
 *   ),
 *   bundle_label = @Translation("PAR Legal Entity type"),
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
 *   base_table = "par_legal_entities",
 *   data_table = "par_legal_entities_field_data",
 *   revision_table = "par_legal_entities_revision",
 *   revision_data_table = "par_legal_entities_field_revision",
 *   admin_permission = "administer par_data_legal_entity entities",
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
 *     "collection" = "/admin/content/par_data/par_data_legal_entity",
 *     "canonical" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}",
 *     "edit-form" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}/delete"
 *   },
 *   bundle_entity_type = "par_data_legal_entity_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_legal_entity_type.edit_form"
 * )
 */
class ParDataLegalEntity extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Registered Name.
    $fields['registered_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Registered Name'))
      ->setDescription(t('The registered name of the Legal Entity.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
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
      ->setDisplayConfigurable('form', FALSE);

    // Registered Name.
    $fields['registered_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Registered Number'))
      ->setDescription(t('The registered number of the Legal Entity.'))
      ->setTranslatable(FALSE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Legal Entity Type.
    $fields['legal_entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Legal Entity Type'))
      ->setDescription(t('The type of Legal Entity.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE);

    return $fields;
  }

}
