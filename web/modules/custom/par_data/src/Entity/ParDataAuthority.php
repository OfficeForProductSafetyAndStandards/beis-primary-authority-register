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
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
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
   * Get the contacts for this Authority.
   */
  public function getPerson($primary = FALSE) {
    $people = $this->get('field_person')->referencedEntities();
    $person = !empty($people) ? current($people) : NULL;

    return $primary ? $person : $people;
  }

  /**
   * Get the regulatory functions for this Authority.
   */
  public function getRegulatoryFunction() {
    return $this->get('field_regulatory_function')->referencedEntities();
  }

  /**
   * Get the allowed regulatory functions for this Authority.
   *
   * @deprecated Remove this field.
   */
  public function getAllowedRegulatoryFunction() {
    return $this->get('field_allowed_regulatory_fn')->referencedEntities();
  }

  /**
   * Get the premises for this Authority.
   */
  public function getPremises($primary = FALSE) {
    $addresses = $this->get('field_premises')->referencedEntities();
    $address = !empty($addresses) ? current($addresses) : NULL;

    return $primary ? $address : $addresses;
  }

  /**
   * Get the primary nation for this authority.
   *
   * @param string $nation
   *   The nation we want to add, this should be one of the allowed sub-country types.
   *
   * @return bool
   */
  public function setNation($nation, $force = FALSE) {
    $entity_type = $this->getParDataManager()->getParBundleEntity($this->getEntityTypeId());
    $allowed_types = $entity_type->getAllowedValues('nation');
    if ($nation && isset($allowed_types[$nation])
      && ($this->get('nation')->isEmpty() || $force)) {
      $this->set('nation', $nation);
    }
  }

  public function getAuthorityType() {
    $authority_bundle = $this->getParDataManager()->getParBundleEntity('par_data_authority');

    return $authority_bundle->getAllowedFieldlabel('authority_type', $this->get('authority_type')->getString());
  }

  public function getName() {
    return $this->get('authority_name')->getString();
  }

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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Authority Type.
    $fields['authority_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Authority Type'))
      ->setDescription(t('The type of authority.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the authority belongs to.'))
      ->addConstraint('par_required')
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // ONS Code.
    $fields['ons_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ONS Code'))
      ->setDescription(t('The ONS code for the authority.'))
      ->addConstraint('par_required')
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Comments.
    $fields['comments'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('About the Authority'))
      ->setDescription(t('Comments about this authority.'))
      ->addConstraint('par_required')
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
