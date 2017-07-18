<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_authority entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_authority",
 *   label = @Translation("PAR Authority"),
 *   label_collection = @Translation("PAR Authorities"),
 *   label_singular = @Translation("PAR Authority"),
 *   label_plural = @Translation("PAR Authorities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count authority",
 *     plural = "@count authorities"
 *   ),
 *   bundle_label = @Translation("PAR Authority type"),
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
 *   base_table = "par_authorities",
 *   data_table = "par_authorities_field_data",
 *   revision_table = "par_authorities_revision",
 *   revision_data_table = "par_authorities_field_revision",
 *   admin_permission = "administer par_data_authority entities",
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
 *     "collection" = "/admin/content/par_data/par_data_authority",
 *     "canonical" = "/admin/content/par_data/par_data_authority/{par_data_authority}",
 *     "edit-form" = "/admin/content/par_data/par_data_authority/{par_data_authority}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_authority/{par_data_authority}/delete"
 *   },
 *   bundle_entity_type = "par_data_authority_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_authority_type.edit_form"
 * )
 */
class ParDataAuthority extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Name.
    $fields['authority_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Authority.'))
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

    // Authority Type.
    $fields['authority_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Authority Type'))
      ->setDescription(t('The type of Authority.'))
      ->setRequired(TRUE)
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

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the Authority belongs to.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // ONS Code.
    $fields['ons_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ONS Code'))
      ->setDescription(t('The ONS Code for the Authority.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Primary Person.
    $fields['primary_person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Primary Person'))
      ->setDescription(t('The primary contact for this Authority.'))
      ->setCardinality(1)
      ->setSetting('target_type', 'par_data_person')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'person' => 'person'
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Person.
    $fields['person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Alternative Person'))
      ->setDescription(t('The alternative contacts for this Authority.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_person')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'person' => 'person'
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 6,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Premises.
    $fields['premises'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Premises'))
      ->setDescription(t('The premises of this Authority.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_premises')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'premises' => 'premises'
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 7,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    return $fields;
  }

}
