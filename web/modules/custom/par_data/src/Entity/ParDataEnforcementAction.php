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
 *       "default" = "Drupal\par_data\Form\ParDataForm",
 *       "add" = "Drupal\par_data\Form\ParDataForm",
 *       "edit" = "Drupal\par_data\Form\ParDataForm",
 *       "delete" = "Drupal\par_data\Form\ParDataDeleteForm",
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
   * Get the blocked advice for this Enforcement Action.
   */
  public function getBlockedAdvice() {
    return $this->get('field_blocked_advices')->referencedEntities();
  }

  /**
   * Get the action referrals for this Enforcement Action.
   */
  public function getActionReferral() {
    return $this->get('field_action_referral')->referencedEntities();
  }

  /**
   * Get the regulatory function for this Enforcement Action.
   */
  public function getRegulatoryFunction() {
    return $this->get('field_regulatory_function')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Summary.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Title of the enforcement action.'))
      ->setRequired(TRUE)
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Details.
    $fields['details'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Summary'))
      ->setDescription(t('Details about this enforcement action.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 2,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Enforcement status.
    // {@deprecated} We will use the concept of workflow states going forward.
    $fields['enforcement_action_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('(deprecated) Enforcement Action Status'))
      ->setDescription(t('The status of the current enforcement action.'))
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Enforcement notes.
    $fields['enforcement_action_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Enforcement Action Notes'))
      ->setDescription(t('Notes about this enforcement action.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 4,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Primary Authority status.
    // {@deprecated} We will use the concept of workflow states going forward.
    $fields['primary_authority_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('(deprecated) Primary Authority Status'))
      ->setDescription(t('The status of the primary authority on this action.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // PA notes.
    $fields['primary_authority_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Primary Authority Notes'))
      ->setDescription(t('Notes about this enforcement action from the primary authority.'))
      ->addConstraint('par_required')
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Referral notes.
    $fields['referral_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Referral Notes'))
      ->setDescription(t('Referral notes.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 7,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
