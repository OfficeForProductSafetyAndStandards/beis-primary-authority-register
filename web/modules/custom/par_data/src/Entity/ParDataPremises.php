<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_premises entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_premises",
 *   label = @Translation("PAR Premises"),
 *   label_collection = @Translation("PAR Premises"),
 *   label_singular = @Translation("PAR Premises"),
 *   label_plural = @Translation("PAR Premises"),
 *   label_count = @PluralTranslation(
 *     singular = "@count premises",
 *     plural = "@count premises"
 *   ),
 *   bundle_label = @Translation("PAR Premises type"),
 *   handlers = {
 *     "storage" = "Drupal\trance\TranceStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\par_data\Views\ParDataViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\par_data\Access\ParDataAccessControlHandler",
 *   },
 *   base_table = "par_premises",
 *   data_table = "par_premises_field_data",
 *   revision_table = "par_premises_revision",
 *   revision_data_table = "par_premises_field_revision",
 *   admin_permission = "administer par_data_premises entities",
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
 *     "collection" = "/admin/content/par_data/par_data_premises",
 *     "canonical" = "/admin/content/par_data/par_data_premises/{par_data_premises}",
 *     "edit-form" = "/admin/content/par_data/par_data_premises/{par_data_premises}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_premises/{par_data_premises}/delete"
 *   },
 *   bundle_entity_type = "par_data_premises_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_premises_type.edit_form"
 * )
 */
class ParDataPremises extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Address.
    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(t('Address'))
      ->setDescription(t('The address details.'))
      ->setCardinality(1)
      ->setSetting('available_countries', ['GB' => 'GB'])
      ->setSetting('fields',
        [
          "organization" => "0",
          "dependentLocality" => "0",
          "sortingCode" => "0",
          "familyName" => "0",
          "langcode_override" => "",
          "administrativeArea" => "administrativeArea",
          "additionalName" => "0",
          "locality" => "locality",
          "addressLine1" => "addressLine1",
          "postalCode" => "postalCode",
          "addressLine2" => "addressLine2",
          "givenName" => "0",
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'address_default',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the Address in is.'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Nation.
    $fields['uprn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Unique Property Reference Number'))
      ->setDescription(t('The unique reference number for the property.'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
