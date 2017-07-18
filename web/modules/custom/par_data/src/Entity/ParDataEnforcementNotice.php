<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_enforcement_notice entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_enforcement_notice",
 *   label = @Translation("PAR Enforcement Notice"),
 *   label_collection = @Translation("PAR Enforcement Notices"),
 *   label_singular = @Translation("PAR Enforcement Notice"),
 *   label_plural = @Translation("PAR Enforcement Notices"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enforcement notice",
 *     plural = "@count enforcement notices"
 *   ),
 *   bundle_label = @Translation("PAR Enforcement Notice type"),
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
 *   base_table = "par_enforcement_notices",
 *   data_table = "par_enforcement_notices_field_data",
 *   revision_table = "par_enforcement_notices_revision",
 *   revision_data_table = "par_enforcement_notices_field_revision",
 *   admin_permission = "administer par_data_enforcement_notice entities",
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
 *     "collection" = "/admin/content/par_data/par_data_enforcement_notice",
 *     "canonical" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}",
 *     "edit-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/delete"
 *   },
 *   bundle_entity_type = "par_data_enforcement_notice_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_enforcement_notice_type.edit_form"
 * )
 */
class ParDataEnforcementNotice extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Notice Type
    $fields['notice_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Notice Type'))
      ->setDescription(t('The type of Enforcement Notice.'))
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

    // Notice Date
    $fields['notice_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Notice Date'))
      ->setDescription(t('The date this Enforcement Notice was issued.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Reference to Primary Authority
    $fields['primary_authority'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Primary Authority'))
      ->setDescription(t('The Primary Authority that issued this Enforcement Notice.'))
      ->setCardinality(1)
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
        'weight' => 3,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Enforcing Authority
    $fields['enforcing_authority'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Enforcing Authority'))
      ->setDescription(t('The Enforcing Authority that is charged with following up on this Enforcement Notice.'))
      ->setCardinality(1)
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
        'weight' => 4,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    // Reference to Legal Entity
    $fields['legal_entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Legal Entities'))
      ->setDescription(t('The Legal Entities this Enforcement Notice is issued to.'))
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
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ));

    return $fields;
  }

}
