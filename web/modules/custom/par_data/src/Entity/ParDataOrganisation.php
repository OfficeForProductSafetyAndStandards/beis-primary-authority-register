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

  /**
   * @var array
   *   An array of entity relationships that are dependent on this entity.
   */
  protected $dependents = [
    'par_data_address',
    'par_data_person',
    'par_data_legal_entity',
  ];

  /**
   * Get the contacts for this Organisation.
   */
  public function getPerson($primary = FALSE) {
    $people = $this->get('field_person')->referencedEntities();
    $person = !empty($people) ? current($people) : NULL;

    return $primary ? $person : $people;
  }

  /**
   * Get the legal entites for this Organisation.
   */
  public function getLegalEntity($single = FALSE) {
    $legal_entities = $this->get('field_legal_entity')->referencedEntities();
    $legal_entity = !empty($legal_entities) ? current($legal_entities) : NULL;

    return $single ? $legal_entity : $legal_entities;
  }

  /**
   * Get all the legal entites associated with the partnership.
   *
   * @return array()
   *   An array containing all the legal entities keyed by entity id's associated with the current partnership.
   */
  public function getPartnershipLegalEntities() {
    $partnership_legal_entities = $this->getLegalEntity();
    $legal_obj_list = array();

    foreach ($partnership_legal_entities as $key => $current_legal_entity) {
      $legal_obj_list[$current_legal_entity->get('id')->getString()] =  $current_legal_entity->get('registered_name')->getString();
    }
    return $legal_obj_list;
  }

  /**
   * Add a legal entity for this Organisation.
   *
   * @param ParDataLegalEntity $legal_entity
   *   A PAR Legal Entity to add.

   */
  public function addLegalEntity(ParDataLegalEntity $legal_entity) {
    $legal_entities = $this->getLegalEntity();
    $legal_entities[] = $legal_entity;
    $this->set('field_legal_entity', $legal_entities);
  }

  /**
   * Get the premises for this Organisation.
   */
  public function getPremises($single = FALSE) {
    $premises = $this->get('field_premises')->referencedEntities();
    $premises_singular = !empty($premises) ? current($premises) : NULL;

    return $single ? $premises_singular : $premises;
  }

  /**
   * Get the SIC Code for this Organisation.
   */
  public function getSicCode($single = FALSE) {
    $sic_codes = $this->get('field_sic_code')->referencedEntities();
    $sic_code = !empty($sic_codes) ? current($sic_codes) : NULL;

    return $single ? $sic_code : $sic_codes;
  }

  /**
   * Helper fn to check if a PAR Organisation is a coordinated member.
   *
   * @return bool
   */
  public function isCoordinatedMember() {
    $query = \Drupal::entityQuery('par_data_coordinated_business');

    $query->condition('field_organisation', [$this->id()], 'IN');

    return $query->count()->execute() >= 1 ? TRUE : FALSE;
  }

  /**
   * Get the primary nation for this organisation.
   *
   * @return bool
   */
  public function getNation() {
    if (!$this->get('nation')->isEmpty()) {
      $nation = $this->get('nation')->getString();
    }
    else {
      $premises = $this->getPremises(TRUE);
      $nation = $premises->getNation(TRUE);
    }

    return $nation;
  }

  /**
   * Get the primary nation for this organisation.
   *
   * @return bool
   */
  public function getTradingNames() {
    if (!$this->get('trading_name')->isEmpty()) {
      $trading_names = $this->get('trading_name')->getString();
    }

    return isset($trading_names) ? $trading_names : '';
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
    if ($nation && isset($allowed_types[$nation])
      && ($this->get('nation')->isEmpty() || $force)) {
      $this->set('nation', $nation);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Name.
    $fields['organisation_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Organisation Name'))
      ->setDescription(t('The name of the organisation.'))
      ->addConstraint('par_required')
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
      ->setLabel(t('Number of members'))
      ->setDescription(t('The size of the organisation.'))
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

    // Number of Employees.
    $fields['employees_band'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number of Employees'))
      ->setDescription(t('The band that best represents the number of employees.'))
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

    // Nation.
    $fields['nation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nation'))
      ->setDescription(t('The nation the organisation belongs to.'))
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
      ->setLabel(t('About the organisation'))
      ->setDescription(t('Comment about the organisation.'))
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
        'weight' => 0,
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Trading Name.
    $fields['trading_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Trading Name'))
      ->setDescription(t('The trading names for this organisation.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'max_length' => 500,
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Coordinator type.
    $fields['coordinator_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coordinator Type'))
      ->setDescription(t('The type of coordinator.'))
      ->addConstraint('par_required')
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Coordinator number.
    $fields['coordinator_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Coordinator Number'))
      ->setDescription(t('Number of eligible coordinators.'))
      ->addConstraint('par_required')
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
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
