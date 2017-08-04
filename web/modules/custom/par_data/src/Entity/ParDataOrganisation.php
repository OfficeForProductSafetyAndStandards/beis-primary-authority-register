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
 *     "views_data" = "Drupal\par_data\Views\ParDataViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
 *     },
 *     "access" = "Drupal\par_data\Access\ParDataAccessControlHandler",
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

  public function getPerson() {
    return $this->get('field_person')->referencedEntities();
  }

  public function getLegalEntity() {
    return $this->get('field_legal_entity')->referencedEntities();
  }

  public function addLegalEntity($legal_entity) {
    $legal_entities = $this->getLegalEntity();
    $legal_entities[] = $legal_entity;
    $this->set('field_legal_entity', $legal_entities);
  }

  public function getPremises() {
    return $this->get('field_premises')->referencedEntities();
  }

  public function getSicCode() {
    return $this->get('field_sic_code')->referencedEntities();
  }
  /**
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Name.
    $fields['organisation_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Organisation Name'))
      ->setDescription(t('The name of the organisation.'))
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

    // Size.
    $fields['size'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Size'))
      ->setDescription(t('The size of the organisation.'))
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

    // Number of Employees.
    $fields['employees_band'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number of Employees'))
      ->setDescription(t('The band that best represents the number of employees.'))
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

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the organisation belongs to.'))
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
      ->setDescription(t('Comments about this organisation.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Premises Mapped.
    $fields['premises_mapped'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Premises Mapped'))
      ->setDescription(t('Whether premises has been mapped.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Trading Name.
    $fields['trading_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Trading Name'))
      ->setDescription(t('The trading names for this organisation.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
