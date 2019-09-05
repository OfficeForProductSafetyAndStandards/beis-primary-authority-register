<?php

namespace Drupal\par_data\Entity;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_premises entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_premises",
 *   label = @Translation("PAR Premises"),
 *   label_collection = @Translation("PAR Premises"),
 *   label_singular = @Translation("PAR Premises"),
 *   label_plural = @Translation("PAR Premises"),
 *   label_count = @PluralTranslation(
 *     singular = "@count premises",
 *     plural = "@count premises"
 *   ),
 *   bundle_label = @Translation("PAR Premises type"),
 *   handlers = {
 *     "storage" = "Drupal\par_data\ParDataStorage",
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
 *   base_table = "par_premises",
 *   data_table = "par_premises_field_data",
 *   revision_table = "par_premises_revision",
 *   revision_data_table = "par_premises_field_revision",
 *   admin_permission = "administer par_data_premises entities",
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
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "collection" = "/admin/content/par_data/par_data_premises",
 *     "canonical" = "/admin/content/par_data/par_data_premises/{par_data_premises}",
 *     "edit-form" = "/admin/content/par_data/par_data_premises/{par_data_premises}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_premises/{par_data_premises}/delete"
 *   },
 *   bundle_entity_type = "par_data_premises_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_premises_type.edit_form"
 * )
 */
class ParDataPremises extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // No relationships should be followed, this is one of the lowest tier entities.
        return FALSE;

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * Get the primary nation for this organisation.
   *
   * @return bool
   */
  public function getNation() {
    return !$this->get('nation')->isEmpty() ? $this->get('nation')->getString() : NULL;
  }

  /**
   * Get the primary nation for this organisation.
   *
   * @param string $nation
   *   The nation we want to add, this should be one of the allowed sub-country types.
   *
   * @return bool
   */
  public function setNation($nation, $force = FALSE) {
    $entity_type = $this->getParDataManager()->getParBundleEntity($this->getEntityTypeId());
    $allowed_types = $entity_type->getAllowedValues('nation');
    if ($nation && isset($allowed_types[$nation])) {
      $this->set('nation', $nation);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Address.
    $fields['address'] = BaseFieldDefinition::create('address')
      ->setLabel(t('Address'))
      ->setDescription(t('The address details.'))
      ->setRevisionable(TRUE)
      ->setCardinality(1)
      ->setSettings([
        'available_countries' => [],
        'fields' => [
          "organization" => "0",
          "dependentLocality" => "0",
          "sortingCode" => "0",
          "familyName" => "0",
          "langcode_override" => "0",
          "administrativeArea" => "administrativeArea",
          "additionalName" => "0",
          "locality" => "locality",
          "addressLine1" => "addressLine1",
          "postalCode" => "postalCode",
          "addressLine2" => "addressLine2",
          "givenName" => "0",
          "country_code" => "countryCode",
        ],
        'field_overrides' => [
          "organization" => ['override' => 'hidden'],
          "dependentLocality" => ['override' => 'hidden'],
          "sortingCode" => ['override' => 'hidden'],
          "familyName" => ['override' => 'hidden'],
          "langcode_override" => ['override' => 'hidden'],
          "administrativeArea" => ['override' => 'optional'],
          "additionalName" => ['override' => 'hidden'],
          "locality" => ['override' => 'optional'],
          "addressLine1" => ['override' => 'required'],
          "postalCode" => ['override' => 'required'],
          "addressLine2" => ['override' => 'optional'],
          "givenName" => ['override' => 'hidden'],
          "country_code" => ['override' => 'optional'],
        ],
        'langcode_override' => '',
      ])
      ->setDisplayOptions('form', array(
        'type' => 'address_default',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the Address in is.'))
      ->addConstraint('par_required')
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Unique Property Reference Number.
    $fields['uprn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Unique Property Reference Number'))
      ->setDescription(t('The unique reference number for the property.'))
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
