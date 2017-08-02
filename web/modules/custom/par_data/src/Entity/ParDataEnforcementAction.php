<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_enforcement_action entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_enforcement_action",
 *   label = @Translation("PAR Enforcement Action"),
 *   label_collection = @Translation("PAR Enforcement Actions"),
 *   label_singular = @Translation("PAR Enforcement Action"),
 *   label_plural = @Translation("PAR Enforcement Actions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enforcement action",
 *     plural = "@count enforcement actions"
 *   ),
 *   bundle_label = @Translation("PAR Enforcement Action type"),
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
 *   base_table = "par_enforcement_actions",
 *   data_table = "par_enforcement_actions_field_data",
 *   revision_table = "par_enforcement_actions_revision",
 *   revision_data_table = "par_enforcement_actions_field_revision",
 *   admin_permission = "administer par_data_enforcement_action entities",
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
 *     "collection" = "/admin/content/par_data/par_data_enforcement_action",
 *     "canonical" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}",
 *     "edit-form" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}/delete"
 *   },
 *   bundle_entity_type = "par_data_enforcement_action_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_enforcement_action_type.edit_form"
 * )
 */
class ParDataEnforcementAction extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Summary.
    $fields['title'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Title'))
      ->setDescription(t('Title of the enforcement action.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 1,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Details.
    $fields['details'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Summary'))
      ->setDescription(t('Details about this enforcement action.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Enforcement status.
    $fields['ea_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Enforcement Action Status'))
      ->setDescription(t('The status of the current enforcement action.'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Enforcement notes.
    $fields['ea_notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Enforcement Action Notes'))
      ->setDescription(t('Notes about this enforcement action.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // PA status.
    $fields['pa_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Primary Authority Status'))
      ->setDescription(t('The status of the primary authority on this action.'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // PA notes.
    $fields['pa_notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Primary Authority Notes'))
      ->setDescription(t('Notes about this enforcement action from the primary authority.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Referral notes.
    $fields['referral_notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Referral Notes'))
      ->setDescription(t('Referral notes.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 3,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Blocked by advice.
    $fields['blocked_advice'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Blocked by Advice'))
      ->setDescription(t('The advice that is blocking this action.'))
      ->setCardinality(1)
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
        'weight' => 4,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    // Referred from action.
    $fields['action_referral'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Action Referred From'))
      ->setDescription(t('The action relating to this action.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'par_data_enforcement_action')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'enforcement_action' => 'enforcement_action',
          ]
        ]
      )
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 4,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);


    // Reference to Regulatory Function.
    $fields['regulatory_function'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Regulatory Function'))
      ->setDescription(t('The Regulatory Function this notice is relevant to.'))
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setSetting('target_type', 'par_data_regulatory_function')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings',
        [
          'target_bundles' => [
            'regulatory_function' => 'regulatory_function'
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
