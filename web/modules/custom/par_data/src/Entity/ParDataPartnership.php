<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_partnership entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_partnership",
 *   label = @Translation("PAR Partnership"),
 *   label_collection = @Translation("PAR Partnerships"),
 *   label_singular = @Translation("PAR Partnership"),
 *   label_plural = @Translation("PAR Partnerships"),
 *   label_count = @PluralTranslation(
 *     singular = "@count partnership",
 *     plural = "@count partnerships"
 *   ),
 *   bundle_label = @Translation("PAR Partnership type"),
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
 *   base_table = "par_partnerships",
 *   data_table = "par_partnerships_field_data",
 *   revision_table = "par_partnerships_revision",
 *   revision_data_table = "par_partnerships_field_revision",
 *   admin_permission = "administer par_data_partnership entities",
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
 *     "collection" = "/admin/content/par_data/par_data_partnership",
 *     "canonical" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}",
 *     "edit-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/delete"
 *   },
 *   bundle_entity_type = "par_data_partnership_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_partnership_type.edit_form"
 * )
 */
class ParDataPartnership extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Partnership Type
    $fields['partnership_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Partnership Type'))
      ->setDescription(t('The type of Partnership.'))
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
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Partnership Status
    $fields['partnership_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Partnership Status'))
      ->setDescription(t('The current status of the Partnership Plan itself. For example, current, expired, replaced.'))
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

    // About Partnership
    $fields['about_partnership'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('About Partnership'))
      ->setDescription(t('Details about this Partnership.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 25,
        'settings' => [
          'rows' => 3,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Communication by Email
    $fields['communication_email'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Communication by E-mail'))
      ->setDescription(t('Whether to allow contact by e-mail for this Partnership.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 4,
      ]);

    // Communication by Phone
    $fields['communication_phone'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Communication by Phone'))
      ->setDescription(t('Whether to allow contact by phone for this Partnership.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 5,
      ]);

    // Communication Notes
    $fields['communication_notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Communication Notes'))
      ->setDescription(t('Additional notes and communication preferences for this Partnership.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 6,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Approved Date
    $fields['approved_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Approved Date'))
      ->setDescription(t('The date this Partnership was approved.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Expertise Details
    $fields['expertise_details'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Expertise Details'))
      ->setDescription(t('Details about the expertise for this Partnership.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 8,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Partnership Status
    $fields['cost_recovery'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Cost Recovery'))
      ->setDescription(t('How is the cost recovered by for this Partnership.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 9,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Rejected Comment
    $fields['reject_comment'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Reject Comment'))
      ->setDescription(t('Comments about why this Partnership was rejected.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 10,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Recovation Source
    $fields['revocation_source'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Revocation Source'))
      ->setDescription(t('Who was responsible for revoking this Partnership.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Recovation Date
    $fields['revocation_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Recovation Date'))
      ->setDescription(t('The date this Partnership was revoked.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Revocation Reason
    $fields['revocation_reason'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Revocation Reason'))
      ->setDescription(t('Comments about why this Partnership was revoked.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 13,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Authority Change Comment
    $fields['authority_change_comment'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Authority Change Comment'))
      ->setDescription(t('Comments by the Authority when this Partnership was changed.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 14,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Organisation Change Comment
    $fields['organisation_change_comment'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Organisation Change Comment'))
      ->setDescription(t('Comments by the Organisation when this Partnership was changed.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 15,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Organisation
    $fields['organisation'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Organisation'))
      ->setDescription(t('The Organisations for this Partnership.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_organisation')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'coordinator' => 'coordinator',
            'business' => 'business',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 16,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Authority
    $fields['authority'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authorities'))
      ->setDescription(t('The Authorities for this Partnership.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_authority')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'authority' => 'authority',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 17,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Advice
    $fields['advice'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Advice'))
      ->setDescription(t('The Advice for this Partnership.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_advice')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'advice' => 'advice',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 18,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Inspection Plan
    $fields['inspection_plan'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Inspection Plan'))
      ->setDescription(t('The Inspection Plans for this Partnership.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_inspection_plan')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'inspection_plan' => 'inspection_plan',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 19,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Regulatory Area
    $fields['regulatory_area'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Regulatory Area'))
      ->setDescription(t('The Regulatory Areas for this Partnership.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_regulatory_area')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'regulatory_area' => 'regulatory_area',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 20,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Person
    $fields['person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Contacts'))
      ->setDescription(t('The contacts for this Partnership.'))
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
        'weight' => 21,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    return $fields;
  }

}
