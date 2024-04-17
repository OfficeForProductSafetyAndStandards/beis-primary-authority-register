<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\registered_organisations\DataException;
use Drupal\registered_organisations\Organisation;
use Drupal\registered_organisations\OrganisationInterface;
use Drupal\registered_organisations\OrganisationManagerInterface;
use Drupal\registered_organisations\OrganisationRegisterInterface;
use Drupal\registered_organisations\RegisterException;
use Drupal\registered_organisations\TemporaryException;
use Symfony\Component\Serializer\SerializerInterface;

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
   * Get the registered organisation manager.
   *
   * @return \Drupal\registered_organisations\OrganisationManagerInterface
   */
  public function getOrganisationManager(): OrganisationManagerInterface {
    return \Drupal::service('registered_organisations.organisation_manager');
  }

  /**
   * Get the par data manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Get the serialization service.
   *
   * @return \Symfony\Component\Serializer\SerializerInterface
   */
  public function getSerializer(): SerializerInterface {
    return \Drupal::service('serializer');
  }

  /**
   * {@inheritdoc}
   *
   * Ensure that we can not create duplicates of legal entities with the same companies house number.
   */
  public static function create(array $values = []) {
    $entity = parent::create($values);

    // Update legacy legal entities.
    try {
      $entity->updateLegacyEntities();
    }
    catch (RegisterException | TemporaryException | DataException $ignored) {
      // Catch all errors silently.
    }

    // De-duplicate the entity.
    return $entity->deduplicate();
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Update legacy legal entities.
    try {
      $this->updateLegacyEntities();
    }
    catch (RegisterException | TemporaryException | DataException $ignored) {
      // Catch all errors silently.
    }

    // Ensure all required fields are up-to-date with the organisation profile.
    $this->lookup();
  }

  /**
   * @return ParDataEntityInterface
   *   The legal entity.
   */
  public function lookup() {
    // Lookup any registered organisations and update the information based on
    // the returned organisation profile.
    if ($this->isRegisteredOrganisation() && $profile = $this->lookupOrganisationProfile()) {
      // Save the true values from the registry.
      $this->set('registered_name', $profile->getName());
      $this->set('registered_number', $profile->getId());
      $this->set('legal_entity_type', $profile->getType());
    }

    return $this;
  }

  /**
   * Tries to de-duplicate the current legal entity.
   *
   * @return ParDataEntityInterface
   *   The original legal entity OR
   *   the duplicate legal entity if found.
   */
  public function deduplicate(): ParDataEntityInterface {
    // The de-duplication parameters will vary depending on the registry type.
    switch ($this->getRegisterId()) {
      case 'companies_house':
      case 'charity_commission':
        $registered_number = $this->get('registered_number')->getString();
        if (!empty($registered_number)) {
          $properties = [
            'registry' => $this->getRegisterId(),
            'registered_number' => $registered_number,
          ];
        }

        break;

      case self::DEFAULT_REGISTER:
        $registered_name = $this->get('registered_name')->getString();
        if (!empty($registered_name)) {
          $properties = [
            'registry' => $this->getRegisterId(),
            'registered_name' => $registered_name,
          ];
        }
        break;

      default:
        $registered_number = $this->get('registered_number')->getString();
        if (!empty($registered_number)) {
          $properties = [
            'registry' => $this->getRegisterId(),
            'registered_number' => $registered_number,
          ];
        }
    }

    // Check to see if a legal entity already exists with this name or number.
    $legal_entities = !empty($properties) ?
      $this->getEntityTypeManager()->getStorage('par_data_legal_entity')
        ->loadByProperties($properties) : [];

    $id = $this->id();
    // Remove this legal entity from the list.
    $legal_entities = array_filter($legal_entities, function ($legal_entity) use ($id) {
      return $legal_entity->id() !== $id;
    });

    // If there are any duplicates return these instead.
    if (!empty($legal_entities)) {
      // Sort so it always returns the same result.
      sort($legal_entities);
      return current($legal_entities);
    }

    return $this;
  }

  /**
   * Whether this legal entity is editable.
   *
   *  - If it isn't yet saved
   *  - If it is a legacy entity.
   *
   * @return bool
   */
  public function isEditable(): bool {
    // If the legal entity is new and not yet saved.
    if ($this->isNew()) {
      return TRUE;
    }

    // All legacy entities can be edited.
    if ($this->isLegacyEntity()) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Whether the legal entity is a registered organisation.
   *
   * @return bool
   */
  public function isRegisteredOrganisation(): bool {
    // Known unregistered values.
    if ($this->getRegisterId() === self::DEFAULT_REGISTER) {
      return FALSE;
    }

    // Check that the register is valid.
    try {
      $definition = $this->getOrganisationManager()
        ->getDefinition($this->getRegisterId());
      return ($definition !== NULL);

    }
    catch (PluginNotFoundException $e) {
      return FALSE;
    }
  }

  /**
   * Get the cached Organisation Profile.
   *
   * @return \Drupal\registered_organisations\OrganisationInterface|null
   */
  public function getOrganisationProfile(): ?OrganisationInterface {
    try {
      $data = !$this->get('organisation')->isEmpty() ?
        $this->get('organisation')->first()->getValue() :
        NULL;
      $register = $this->isRegisteredOrganisation() ?
        $this->getOrganisationManager()->getRegistry($this->getRegisterId()) :
        NULL;

      if (!empty($data) && $register instanceof OrganisationRegisterInterface) {
        $register = $this->getOrganisationManager()->getRegistry($this->getRegisterId());
        return new Organisation($register, $data);
      }
    }
    catch (MissingDataException | DataException $e) {
      return NULL;
    }

    return NULL;
  }

  /**
   * Get the registered organisation profile.
   *
   * @return ?OrganisationInterface
   */
  public function lookupOrganisationProfile($cacheable = TRUE): ?OrganisationInterface {
    // Organisation profiles only exist for registered organisations.
    if ($this->isRegisteredOrganisation()) {
      // Try to return the cached profile.
      if ($cacheable && $profile = $this->getOrganisationProfile()) {
        return $profile;
      }

      try {
        $profile = $this->getOrganisationManager()
          ->lookupOrganisation($this->getRegisterId(), (string) $this->getId());
      }
      catch (\Exception $e) {

      }

      // Save the profile.
      if ($profile instanceof OrganisationInterface) {
        $this->setOrganisation($profile);
        return $profile;
      }
    }

    return NULL;
  }

  /**
   * Store the serialized Organisation Profile.
   *
   * @param \Drupal\registered_organisations\OrganisationInterface $profile
   */
  protected function setOrganisation(OrganisationInterface $profile) {
    $this->set('organisation', $profile->getData());
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
   * Get the organisation id of the legal entity.
   *
   * @return string|int
   */
  public function getId(): string|int {
    return $this->isRegisteredOrganisation() ?
      trim($this->get('registered_number')->getString()) : $this->id();
  }

  /**
   * Get the registered number of the legal entity.
   *
   * @return string
   *   The registered number of the legal entity.
   */
  public function getRegisteredNumber(): string {
    return $this->isRegisteredOrganisation() ?
      (string) $this->lookupOrganisationProfile()?->getId() :
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
      (string) $this->lookupOrganisationProfile()?->getName() :
      $this->get('registered_name')->getString();
  }

  /**
   * Get the type of the legal entity.
   *
   * @return string
   *   The type of the legal entity.
   */
  public function getType(bool $processed = TRUE): string {
    if ($this->isRegisteredOrganisation()) {
      return (string) $this->lookupOrganisationProfile()?->getType($processed);
    }
    else {
      $bundle_entity = $this->type?->entity;
      $value = $this->get('legal_entity_type')->getString();
      return $processed ?
        $bundle_entity?->getFieldLabel('legal_entity_type', $value) :
        $value;
    }
  }

  /**
   * Get the status of the legal entity.
   *
   * @return string
   *   The status of the legal entity.
   */
  public function getStatus(): string {
    return $this->isRegisteredOrganisation() ?
      (string) $this->lookupOrganisationProfile()?->getStatus() :
      self::DEFAULT_STATUS;
  }

  /**
   * Get the processed value for the type of the legal entity.
   *
   * @return string
   *   The type of the legal entity.
   */
  public function processStatus(): string {
    if ($this->isRegisteredOrganisation()) {
      return (string) $this->lookupOrganisationProfile()?->getType(TRUE);
    }
    else {
      $bundle_entity = $this->type?->entity;
      $value = $this->get('legal_entity_status')->getString();
      return $bundle_entity?->getFieldLabel('legal_entity_status', $value);
    }
  }

  /**
   * Get the SIC classification of the legal entity.
   *
   * @return array
   *   The classification of the legal entity.
   */
  public function getClassification(): array {
    return $this->isRegisteredOrganisation() ?
      (array) $this->lookupOrganisationProfile()?->getClassification() :
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
   * Migrate legacy legal entities.
   *
   * Legacy legal entities are those created before the registered_organisations
   * plugins were used to match the data with external registers.
   *
   * They are characterised by no value for the 'registry' field, and have a fixed
   * number of potential values for the 'legal_entity_type' field which correspond
   * to whether the legal entity is a registered organisation.
   *
   * @todo Revisit once the majority of legacy legal entities are updated
   *
   * @throws \Drupal\registered_organisations\DataException|TemporaryException|RegisterException
   *   In the event of an error looking up the organisation.
   *
   * @return bool
   *   Whether the legal entity was updated.
   */
  public function updateLegacyEntities(): bool {
    // If no registry is set, and the legal_entityCheck for legacy legal entity types.
    if ($this->isLegacyEntity() && $register = $this->convertLegacyTypeToRegister()) {
      switch ($register) {
        case self::DEFAULT_REGISTER:
          // Set the register value if a name was found.
          $name = $this->get('registered_name')->getString();
          if (!empty($name)) {
            $this->set('registry', self::DEFAULT_REGISTER);

            return TRUE;
          }

          break;

        default:
          // Lookup the legacy legal entity.
          $id = $this->get('registered_number')->getString();
          $processed_id = trim($id);
          $profile = $this->getOrganisationManager()->lookupOrganisation($register, $processed_id);

          // Set the register value if an organisation profile is found.
          if ($profile instanceof OrganisationInterface) {
            $this->set('registry', $register);

            // Store the profile data with the legal entity.
            $this->setOrganisation($profile);

            return TRUE;
          }

          break;

      }
    }

    return FALSE;
  }

  /**
   * Determines whether this is a legacy legal entity that needs updating.
   *
   * @todo Revisit once the majority of legacy legal entities are updated.
   *
   * @return bool
   */
  public function isLegacyEntity(): bool {
    return $this->get('registry')->isEmpty();
  }

  /**
   * Determines whether this is a legacy legal entity that needs updating.
   *
   * @todo Revisit once the majority of legacy legal entities are updated.
   *
   * @return ?string
   *   Returns the new register based on the legacy type, or null if
   *   an invalid value is found for the legacy type.
   *   Null values indicate the legal entity will need manual correction.
   */
  protected function convertLegacyTypeToRegister(): ?string {
    // Legacy registered organisation mapping.
    $legacy_types = [
      'companies_house' => [
        'partnership' => 'Partnership',
        'limited_company' => 'Limited Company',
        'public_limited_company' => 'Public Limited Company',
        'limited_partnership' => 'Limited Partnership',
        'limited_liability_partnership' => 'Limited Liability Partnership',
      ],
      'charity_commission' => [
        'registered_charity' => 'Registered Charities',
      ],
      self::DEFAULT_REGISTER => [
        'other' => 'Other',
        'sole_trader' => 'Sole Trader',
      ],
    ];

    // If no type is set assume the default.
    $type = $this->get('legal_entity_type')->getString();
    if (empty($type)) {
      return NULL;
    }

    // Search the legacy types, if the type value matches one of the
    // keys or values then it should be converted with that register plugin.
    foreach ($legacy_types as $register => $types) {
      if (isset($types[$type]) || in_array($type, $types)) {
        return $register;
      }
    }

    return NULL;
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

    // Organisation profile.
    $fields['organisation'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Organisation Profile'))
      ->setDescription(t('A local cache of the organisation profile retrieved from the register.'))
      ->setRequired(FALSE)
      ->setDefaultValue('');

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
