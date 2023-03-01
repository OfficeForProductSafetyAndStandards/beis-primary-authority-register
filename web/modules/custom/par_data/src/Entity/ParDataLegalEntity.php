<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataException;
use Drupal\par_data\ParDataManager;
use Drupal\par_data\ParDataRelationship;
use Drupal\par_validation\Plugin\Validation\Constraint\ParRequired;

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
   * PAR-1943 Temporary mapping of legal entity type to registry. Used by temporary self::preSave() to set value of
   * registry base field.
   *
   * To be removed once external registry integration is complete.
   */
  const TYPE_TO_REGISTRY_MAP = [
    'partnership' => 'internal',
    'registered_charity' => 'charity_commission',
    'sole_trader' => 'internal',
    'limited_company' => 'companies_house',
    'public_limited_company' => 'companies_house',
    'limited_partnership' => 'companies_house',
    'limited_liability_partnership' => 'companies_house',
    'other' => 'internal',
  ];

  /**
   * {@inheritdoc}
   *
   * Ensure that we can not create duplicates of legal entities with the same companies house number.
   *
   * @note PAR-1915 - If register is passed in then we know we are dealing with the authority partnership amend journey.
   *       we first check to see if the legal entity already exists before creating a new LE instance.
   *
   * @todo Need to fix the handling of 'legacy' calls to create LE instances with proper register and legal_entity_type values.
   */
  public static function create(array $values = []) {

    if (isset($values['registry'])) {

      // Check that we have all the values.
      if ($values['register'] == 'internal') {
        if (!isset($values['legal_entity_type']) || !isset($values['registered_name'])) {
          throw new ParDataException('Bad parameters: ' . print_r($values, TRUE));
        }
      }
      else {
        if (!isset($values['legal_entity_type']) || !isset($values['registered_number']) || !isset($values['registered_name'])) {
          throw new ParDataException('Bad parameters: ' . print_r($values, TRUE));
        }
      }

      // Lookup any existing instance.
      $parStorage = \Drupal::entityTypeManager()->getStorage('par_data_legal_entity');

      $query = $parStorage->getQuery()
        ->condition('status', 1)
        ->condition('registry', $values['registry'])
        ->sort('created', 'ASC') // Oldest first any others are duplicates that should not exist.
        ->pager(1);

      if ($values['register'] == 'internal') {
        $query->condition('registered_name', $values['registered_name']);
      }
      else {
        $query->condition('registered_number', $values['registered_number']);
      }

      $ids = $query->execute();

      $legalEntities = $parStorage->loadMultiple($ids);

      if (!empty($legalEntities)) {
        return $legalEntities[0];
      }

      return parent::create($values);
    }

    /* @var ParDataManager $par_data_manager */
    $par_data_manager = \Drupal::service('par_data.manager');

    // Check to see if a legal entity already exists with this number.
    $legal_entities = !empty($values['registered_number']) ?
      $par_data_manager->getEntitiesByProperty('par_data_legal_entity', 'registered_number', $values['registered_number']) :
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
   * Lookup a legal entity in PAR.
   *
   * @param $registry
   * @param $type
   * @param $number
   * @param $name

   * @return \Drupal\Core\Entity\EntityInterface|ParDataLegalEntity|null
   */
  public static function find($registry, $type, $number, $name) {

    /* @var ParDataManager $par_data_manager */
    $par_data_manager = \Drupal::service('par_data.manager');

    // Look up internal LEs by name and registered LEs by number.
    if ($registry == 'internal') {
      $legal_entities = $par_data_manager->getEntitiesByProperty('par_data_legal_entity', 'registered_name', $name);
    }
    else {
      $legal_entities = $par_data_manager->getEntitiesByProperty('par_data_legal_entity', 'registered_number', $number);
    }

    // Return first with matching type.
    /* @var ParDataLegalEntity $legal_entity */
    foreach ($legal_entities as $legal_entity) {
      if ($legal_entity->getTypeRaw() == $type) {
        return $legal_entity;
      }
    }
    return NULL;
  }

  public function getName() {
    $name = $this->get('registered_name')->getString();
    return $name;
  }

  public function getRegistry() {
    $registry = $this->get('registry')->getString();
    return $registry;
  }

  public function getRegisteredNumber() {
    $number = $this->get('registered_number')->getString();
    return $number;
  }

  public function getTypeRaw() {
    return $this->get('legal_entity_type')->getString();
  }

  public function getType() {
    $value = $this->getTypeRaw();
    $type = !empty($value) ? $this->getTypeEntity()->getAllowedFieldlabel('legal_entity_type', $value) : NULL;
    return $type;
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
   *
   * @Todo This function needs cater for multiple registries. See PAR-2019.
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

      // Remove this legal entity record.
      $legal_entity->delete();
    }

    // This method will always save the entity.
    $this->save();
  }

  /**
   * {@inheritdoc}
   *
   * PAR-1943 This override is here temporally to maintain the register field until the rest of
   * the external registry integration is completed (PAR-1942).
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    $type = $this->getTypeRaw();

    $registry = self::TYPE_TO_REGISTRY_MAP[$type] ?? 'internal';

    $this->set('registry', $registry);
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
