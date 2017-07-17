<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\Trance;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_advice entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_advice",
 *   label = @Translation("PAR Advice"),
 *   label_collection = @Translation("PAR Advice"),
 *   label_singular = @Translation("PAR Advice"),
 *   label_plural = @Translation("PAR Advice"),
 *   label_count = @PluralTranslation(
 *     singular = "@count advice",
 *     plural = "@count advices"
 *   ),
 *   bundle_label = @Translation("PAR Advice type"),
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
 *   base_table = "par_advice",
 *   data_table = "par_advice_field_data",
 *   revision_table = "par_advice_revision",
 *   revision_data_table = "par_advice_field_revision",
 *   admin_permission = "administer par_data_advice entities",
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
 *     "collection" = "/admin/content/par_data/par_data_advice",
 *     "canonical" = "/admin/content/par_data/par_data_advice/{par_data_advice}",
 *     "edit-form" = "/admin/content/par_data/par_data_advice/{par_data_advice}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_advice/{par_data_advice}/delete"
 *   },
 *   bundle_entity_type = "par_data_advice_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_advice_type.edit_form"
 * )
 */
class ParDataAdvice extends Trance {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Advice Type
    $fields['advice_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Advice Type'))
      ->setDescription(t('The type of advice.'))
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

    // Notes
    $fields['notes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Notes'))
      ->setDescription(t('Notes about this advice.'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_long',
        'weight' => 25,
        'settings' => [
          'rows' => 2,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE);

    // Authority Visible
    $fields['visible_authority'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Authority'))
      ->setDescription(t('Whether this advice is visible to an Authority.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ]);

    // Coordinator Visible
    $fields['visible_coordinator'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Co-ordinator'))
      ->setDescription(t('Whether this advice is visible to a Co-ordinator.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 4,
      ]);

    // Business Visible
    $fields['visible_business'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Business'))
      ->setDescription(t('Whether this advice is visible to a Business.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 5,
      ]);

    return $fields;
  }

}
