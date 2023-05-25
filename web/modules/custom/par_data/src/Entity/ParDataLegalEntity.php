<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataRelationship;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Drupal\registered_organisations\OrganisationRegisterInterface;
use Drupal\registered_organisations\OrganisationInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the par_data_legal_entity entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_legal_entity",
 *   label = @Translation("PAR Legal Entity"),
 *   label_collection = @Translation("PAR Legal Entities"),
 *   label_singular = @Translation("PAR Legal Entity"),
 *   label_plural = @Translation("PAR Legal Entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count legal entity",
 *     plural = "@count legal entities"
 *   ),
 *   bundle_label = @Translation("PAR Legal Entity type"),
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
 *   base_table = "par_legal_entities",
 *   data_table = "par_legal_entities_field_data",
 *   revision_table = "par_legal_entities_revision",
 *   revision_data_table = "par_legal_entities_field_revision",
 *   admin_permission = "administer par_data_legal_entity entities",
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
 *     "collection" = "/admin/content/par_data/par_data_legal_entity",
 *     "canonical" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}",
 *     "edit-form" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_legal_entity/{par_data_legal_entity}/delete"
 *   },
 *   bundle_entity_type = "par_data_legal_entity_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_legal_entity_type.edit_form"
 * )
 */
class ParDataLegalEntity extends ParDataEntity {

  /**
   * The default location for obtaining legal entity data.
   *
   * If no register is set the storage as internal.
   */
  const DEFAULT_REGISTER = 'internal';

  /**
   * The default status for all legal entities.
   */
  const DEFAULT_STATUS = 'active';

  /**
   * Store the organisation profile.
   *
   * @var ?OrganisationInterface
   */
  protected ?OrganisationInterface $profile = NULL;

  /**
   * Get the registered organisation manager.
   *
   * @return OrganisationManagerInterface
   */
  public function getOrganisationManager(): OrganisationManagerInterface {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * {@inheritdoc}
   *
   * Ensure that we can not create duplicates of legal entities with the same companies house number.
   */
  public static function create(array $values = []) {
    $par_data_manger = \Drupal::service('par_data.manager');

    // Check to see if a legal entity already exists with this number.
    $legal_entities = !empty($values['registered_number']) ?
      $par_data_manger->getEntitiesByProperty('par_data_legal_entity', 'registered_number', $values['registered_number']) :
      NULL;

    // Use the first available legal entity if one is found, otherwise
    // create a new record.
    if (!empty($legal_entities)) {
      return current($legal_entities);
    }
    else {
      return parent::create($values);
    }
  }

  /**
   * Migrate legacy legal entities.
   *
   * Legacy legal entities are those created before the registered_organisations
   * plugins were used to match the data with external registers.
   *
   * They are characterised by no value for the 'registry' field, and have a fixed
   * number of potential values for the 'legal_entity_type' field which correspond
   * to whether the legal entity is a registered organisation.
   *
   * @TODO Revisit once the majority of legacy legal entities are updated.
   */
  protected function updateLegacyEntities() {
    // Legacy registered organisation mapping.
    $legacy_registered_types = [
      'partnership' => 'Partnership',
      'limited_company' => 'Limited Company',
      'public_limited_company' => 'Public Limited Company',
      'limited_partnership' => 'Limited Partnership',
      'limited_liability_partnership' => 'Limited Liability Partnership',
    ];

    // Get the entity values needed for lookup.
    $id = $this->get('registered_number')->getString();
    $type = $this->get('legal_entity_type')->getString();

    // If no registry is set, and the legal_entityCheck for legacy legal entity types.
    if ($this->get('registry')->isEmpty() && !empty($id) && !empty($type) &&
        (isset($legacy_registered_types[$type]) ||
        array_search($type, $legacy_registered_types) !== FALSE)) {

      // Lookup the legacy legal entity.
      $profile = $this->getOrganisationManager()
        ->lookupOrganisation('companies_house', $id);

      // Set the register value if an organisation profile is found.
      if ($profile instanceof OrganisationInterface) {
        $this->set('registry', $profile->getRegistry()->getPluginId());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Update legacy legal entities.
    $this->updateLegacyEntities();

    // If this legal entity is a registered organisation.
    if ($profile = $this->getOrganisationProfile()) {
      // Save the true values from the registry.
      $this->set('registered_name', $profile->getName());
      $this->set('registered_number', $profile->getId());
      $this->set('legal_entity_type', $profile->getType());
    }
  }

  /**
   * Get the register id for this legal entity.
   *
   * @return string
   */
  public function getRegisterId(): string {
    return !$this->get('registry')->isEmpty() ?
      $this->get('registry')->getString() : self::DEFAULT_REGISTER;
  }

  /**
   * Whether the legal entity is a registered organisation.
   *
   * @return bool
   */
  public function isRegisteredOrganisation(): bool {
    $definition = $this->getOrganisationManager()
      ->getDefinition($this->getRegisterId(), FALSE);

    return ($definition !== NULL);
  }

  /**
   * Get the registered organisation profile.
   *
   * @return ?OrganisationInterface
   */
  public function getOrganisationProfile(): ?OrganisationInterface {
    if ($this->profile) {
      return $this->profile;
    }

    // Organisation profiles only exist for registered organisations.
    if ($this->isRegisteredOrganisation()) {
      // Get the organisation profile.
      $profile = $this->getOrganisationManager()
        ->lookupOrganisation($this->getRegisterId(), $this->getId());

      // Save the profile.
      if ($profile instanceof OrganisationInterface) {
        $this->profile = $profile;
      }
      return $profile;
    }

    return NULL;
  }

  /**
   * Get the organisation id of the legal entity.
   *
   * @return string|int
   */
  public function getId(): string|int {
    return $this->isRegisteredOrganisation() ?
      $this->get('registered_number')->getString() : $this->id();
  }

  /**
   * Get the registered number of the legal entity.
   *
   * @return string
   *   The registered number of the legal entity.
   */
  public function getRegisteredNumber(): string {
    return $this->isRegisteredOrganisation() ?
      $this->getOrganisationProfile()?->getId() :
      $this->get('registered_number')->getString();
  }

  /**
   * Get the name of the legal entity.
   *
   * @return string
   *   The name of the legal entity.
   */
  public function getName(): string {
    return $this->isRegisteredOrganisation() ?
      $this->getOrganisationProfile()?->getName() :
      $this->get('registered_name')->getString();
  }

  /**
   * Get the name of the legal entity.
   *
   * @return string
   *   The name of the legal entity.
   */
  public function getType(): string {
    return $this->isRegisteredOrganisation() ?
      $this->getOrganisationProfile()?->getType() :
      $this->get('legal_entity_type')->getString();
  }

  /**
   * Get the name of the legal entity.
   *
   * @return string
   *   The name of the legal entity.
   */
  public function getStatus(): string {
    return $this->isRegisteredOrganisation() ?
      $this->getOrganisationProfile()?->getStatus() :
      self::DEFAULT_STATUS;
  }

  /**
   * Get the SIC classification of the legal entity.
   *
   * @return array
   *   The classification of the legal entity.
   */
  public function getClassification(): array {
    return $this->isRegisteredOrganisation() ?
      $this->getOrganisationProfile()?->getClassification() :
      [];
  }

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
   * Merge all legal entities that use the same companies house ID.
   */
  public function mergeLegalEntities() {
    // Lookup related legal entities.
    $legal_entities = !empty($this->getRegisteredNumber()) ? $this->getParDataManager()->getEntitiesByProperty($this->getEntityTypeId(), 'registered_number', $this->getRegisteredNumber()) : [];

    foreach ($legal_entities as $legal_entity) {
      // Skip modifications of the current legal entity.
      if ($legal_entity->id() === $this->id()) {
        continue;
      }

      // Get all the entities that reference this legal entity.
      $relationships = $legal_entity->getRelationships(NULL, NULL, TRUE);
      foreach ($relationships as $relationship) {
        if ($relationship->getRelationshipDirection() === ParDataRelationship::DIRECTION_REVERSE) {
          // Only update the related entity if it does not already reference the updated record.
          $update = TRUE;
          foreach ($relationship->getEntity()->get($relationship->getField()->getName())->referencedEntities() as $e) {
            if ($e->id() === $this->id()) {
              $update = FALSE;
            }
          }

          // Update all entities that reference the soon to be merged legal entity.
          if ($update) {
            $relationship->getEntity()->get($relationship->getField()->getName())->appendItem($this->id());
            $relationship->getEntity()->save();
          }

          // Delete methods check to see if there are any related entities that
          // require this entity, @see ParDataEntity::isDeletable(), all references
          // must be removed before the entity can be deleted.
          $field_items = $relationship->getEntity()->get($relationship->getField()->getName())->getValue();
          if(!empty($field_items)) {
            // Find & remove this person from the referenced entity.
            $key = array_search($legal_entity->id(), array_column($field_items, 'target_id'));
            if (false !== $key && $relationship->getEntity()->get($relationship->getField()->getName())->offsetExists($key)) {
              $relationship->getEntity()->get($relationship->getField()->getName())->removeItem($key);
              $relationship->getEntity()->save();
            }
          }
        }
      }

      // Remove this person record.
      $legal_entity->delete();
    }

    // This method will always save the entity.
    $this->save();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Registry.
    $fields['registry'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Registry'))
      ->setDescription(t('The organisation where the legal entity is registered.'))
      ->setRequired(FALSE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 32,
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

    // Registered Name.
    $fields['registered_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Registered Name'))
      ->setDescription(t('The registered name of the legal entity.'))
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

    // Registered Number.
    $fields['registered_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Registered Number'))
      ->setDescription(t('The registered number of the legal entity.'))
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

    // Legal Entity Type.
    $fields['legal_entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Legal Entity Type'))
      ->setDescription(t('The type of Legal Entity.'))
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
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
