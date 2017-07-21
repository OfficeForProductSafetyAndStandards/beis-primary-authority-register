<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_person entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_person",
 *   label = @Translation("PAR Person"),
 *   label_collection = @Translation("PAR People"),
 *   label_singular = @Translation("PAR Person"),
 *   label_plural = @Translation("PAR People"),
 *   label_count = @PluralTranslation(
 *     singular = "@count person",
 *     plural = "@count people"
 *   ),
 *   bundle_label = @Translation("PAR Person type"),
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
 *   base_table = "par_people",
 *   data_table = "par_people_field_data",
 *   revision_table = "par_people_revision",
 *   revision_data_table = "par_people_field_revision",
 *   admin_permission = "administer par_data_person entities",
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
 *     "collection" = "/admin/content/par_data/par_data_person",
 *     "canonical" = "/admin/content/par_data/par_data_person/{par_data_person}",
 *     "edit-form" = "/admin/content/par_data/par_data_person/{par_data_person}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_person/{par_data_person}/delete"
 *   },
 *   bundle_entity_type = "par_data_person_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_person_type.edit_form"
 * )
 */
class ParDataPerson extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Title.
    $fields['salutation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of this Person.'))
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

    // Name.
    $fields['person_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Person.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Work Phone.
    $fields['work_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Work Phone'))
      ->setDescription(t('The work phone of this Person.'))
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

    // Mobile Phone.
    $fields['mobile_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Mobile Phone'))
      ->setDescription(t('The mobile phone of this Person.'))
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

    // Email.
    $fields['email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('E-mail'))
      ->setDescription(t('The e-mail address of this Person.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Role.
    $fields['role'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Role'))
      ->setDescription(t('The role of this Person.'))
      ->setTranslatable(TRUE)
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
      ->setDisplayConfigurable('form', FALSE);

    return $fields;
  }

}
