<?php

namespace Drupal\par_data\Entity;

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
 *     "views_data" = "Drupal\par_data\Views\TranceViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\par_data\Access\ParDataAccessControlHandler",
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
class ParDataAuthority extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Name.
    $fields['authority_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Authority Name'))
      ->setDescription(t('The name of the authority.'))
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

    // Authority Type.
    $fields['authority_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Authority Type'))
      ->setDescription(t('The type of authority.'))
      ->setRequired(TRUE)
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the authority belongs to.'))
      ->setRequired(TRUE)
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

    // ONS Code.
    $fields['ons_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ONS Code'))
      ->setDescription(t('The ONS code for the authority.'))
      ->setRequired(TRUE)
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
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Comments.
    $fields['comments'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Comments'))
      ->setDescription(t('Comments about this authority.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 5,
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

    // Reference to Person.
    $fields['person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Person'))
      ->setDescription(t('The contacts for this authority. The first person will be the primary contact.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'par_data_person',
        'handler' => 'default',
        'handler_settings' => [
          'target_bundles' => [
            'person' => 'person',
          ],
        ],
      ])
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Reference to Regulatory Function.
    $fields['regulatory_function'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Regulatory Function'))
      ->setDescription(t('The regulatory functions this authority is responsible for.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'par_data_regulatory_function',
        'handler' => 'default',
        'handler_settings' => [
          'target_bundles' => [
            'regulatory_function' => 'regulatory_function',
          ],
        ],
      ])
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Reference to Regulatory Function.
    $fields['allowed_regulatory_function'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Allowed Regulatory Functions'))
      ->setDescription(t('The regulatory functions this authority is allowed to serve.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'par_data_regulatory_function',
        'handler' => 'default',
        'handler_settings' => [
          'target_bundles' => [
            'regulatory_function' => 'regulatory_function',
          ],
        ],
      ])
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Reference to Premises.
    $fields['premises'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Premises'))
      ->setDescription(t('The premises of this organisation. The first premises will be the primary premises'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'par_data_premises',
        'handler' => 'default',
        'handler_settings' => [
          'target_bundles' => [
            'premises' => 'premises',
          ],
        ],
      ])
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 10,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
