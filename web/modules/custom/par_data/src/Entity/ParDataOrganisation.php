<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_organisation entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_organisation",
 *   label = @Translation("PAR Organisation"),
 *   label_collection = @Translation("PAR Organisations"),
 *   label_singular = @Translation("PAR Organisation"),
 *   label_plural = @Translation("PAR Organisations"),
 *   label_count = @PluralTranslation(
 *     singular = "@count organisation",
 *     plural = "@count organisations"
 *   ),
 *   bundle_label = @Translation("PAR Organisation type"),
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
 *   base_table = "par_organisations",
 *   data_table = "par_organisations_field_data",
 *   revision_table = "par_organisations_revision",
 *   revision_data_table = "par_organisations_field_revision",
 *   admin_permission = "administer par_data_organisation entities",
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
 *     "collection" = "/admin/content/par_data/par_data_organisation",
 *     "canonical" = "/admin/content/par_data/par_data_organisation/{par_data_organisation}",
 *     "edit-form" = "/admin/content/par_data/par_data_organisation/{par_data_organisation}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_organisation/{par_data_organisation}/delete"
 *   },
 *   bundle_entity_type = "par_data_organisation_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_organisation_type.edit_form"
 * )
 */
class ParDataOrganisation extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Name.
    $fields['organisation_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Organisation.'))
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

    // Size.
    $fields['size'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Size'))
      ->setDescription(t('The size of the Organisation.'))
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

    // Number of Employees.
    $fields['employees_band'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number of Employees'))
      ->setDescription(t('The band that best represents the number of employees.'))
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

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the Organisation belongs to.'))
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

    // Comments.
    $fields['comments'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Comments'))
      ->setDescription(t('Comments about this Organisation.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 5,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Premises Mapped.
    $fields['premises_mapped'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Premises Mapped'))
      ->setDescription(t('Whether premises has been mapped.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Trading Name.
    $fields['trading_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Trading Name'))
      ->setDescription(t('The trading names for this Organisation.'))
      ->setRequired(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Person.
    $fields['person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Person'))
      ->setDescription(t('The contacts for this Organisation. The first Person will be the primary contact.'))
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
        'weight' => 9,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Premises.
    $fields['premises'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Premises'))
      ->setDescription(t('The premises of this Organisation. The first Premises will be the primary Premises'))
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
        'weight' => 10,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Legal Entity.
    $fields['legal_entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Legal Entity'))
      ->setDescription(t('The Legal Entities represented by this Organisation.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_legal_entity')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'legal_entity' => 'legal_entity'
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 11,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE);

    // Reference to SIC Code.
    $fields['sic_code'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('SIC Code'))
      ->setDescription(t('The SIC Codes this Organisation belongs to.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_sic_code')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'sic_code' => 'sic_code'
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 12,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE);

    return $fields;
  }

}
