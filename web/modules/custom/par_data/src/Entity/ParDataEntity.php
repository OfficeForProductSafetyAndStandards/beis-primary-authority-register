<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Utility\Random;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityEvent;
use Drupal\Core\Entity\EntityEvents;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\par_data\Event\ParDataEvent;
use Drupal\par_data\ParDataException;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_data\ParDataRelationship;
use Drupal\trance\Trance;
use Drupal\Component\Datetime\TimeInterface;

/**
 * Defines the PAR entities.
 *
 * @ingroup par_data
 */
class ParDataEntity extends Trance implements ParDataEntityInterface {

  use LoggerChannelTrait;
  use StringTranslationTrait;
  use RevisionLogEntityTrait;

  const PAR_LOGGER_CHANNEL = 'par';
  const DELETE_FIELD = 'deleted';
  const REVOKE_FIELD = 'revoked';
  const ARCHIVE_FIELD = 'archived';
  const DELETE_REASON_FIELD = 'deleted_reason';
  const REVOKE_REASON_FIELD = 'revocation_reason';
  const ARCHIVE_REASON_FIELD = 'archive_reason';

  const DEFAULT_RELATIONSHIP = 'default';

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * Simple getter to inject the date formatter service.
   *
   * @return DateFormatterInterface
   */
  public function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Get time service.
   *
   * @return \Drupal\Component\Datetime\TimeInterface
   */
  public function getTime() {
    return \Drupal::time();
  }

  /**
   * Get the type entity.
   */
  public function getTypeEntity() {
    return $this->type->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewBuilder() {
    return \Drupal::entityTypeManager()->getViewBuilder($this->getEntityTypeId());
  }

  /**
   * {@inheritdoc}
   */
  public function label() {

    //PAR-988 prevent the page crashing when NULL is returned by getTypeEntity() on the current entity.
    if (is_object($this->getTypeEntity())) {
      $label_fields = $this->getTypeEntity()->getConfigurationElementByType('entity', 'label_fields');
    }

    $labels = [];
    if (isset($label_fields) && is_string($label_fields)) {
      $labels[] = $this->getLabelValue($label_fields);
    }
    else if (isset($label_fields) && is_array($label_fields)) {
      foreach ($label_fields as $field) {
        $labels[] = $this->getLabelValue($field);
      }
    }

    $label = implode(' ', $labels);

    return isset($label) && !empty($label) ? $label : parent::label();
  }

  protected function getLabelValue($value) {
    list($field_name, $property_name) = explode(':', $value . ':');

    if ($this->hasField($field_name)) {
      if ($this->get($field_name)->isEmpty()) {
        return '(none)';
      }
      elseif ($this->get($field_name) instanceof EntityReferenceFieldItemListInterface) {
        $entities = $this->get($field_name)->referencedEntities();
        return !empty($entities) ? current($entities)->label() : '';
      }
      elseif (!empty($property_name)) {
        $value = $this->get($field_name)->getValue();
        return $value && isset(current($value)[$property_name]) ? current($value)[$property_name] : '';
      }
      else {
        return $this->get($field_name)->getString();
      }
    }
    else {
      return $value;
    }
  }

  /**
   * Set a default administrative title for entities where we don't really need one.
   *
   * @return string
   */
  public static function setDefaultTitle() {
    return uniqid();
  }

  /**
   * Will return true if the entity is allowed to exist within the system.
   * Or false if it has been soft-removed.
   *
   * @return bool
   */
  public function isLiving() {
    return !$this->isDeleted() && $this->isTransitioned() && $this->getBoolean('status');
  }

  /**
   * Whether this entity is deleted.
   *
   * @return bool
   */
  public function isDeleted() {
    if ($this->getTypeEntity()->isDeletable() && $this->getBoolean(self::DELETE_FIELD)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Whether this entity is revoked.
   *
   * @return bool
   */
  public function isRevoked() {
    if ($this->getTypeEntity()->isRevokable() && $this->getBoolean(self::REVOKE_FIELD)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Whether this entity is archived.
   *
   * @return bool
   */
  public function isArchived() {
    if ($this->getTypeEntity()->isArchivable() && $this->getBoolean(self::ARCHIVE_FIELD)) {
      return TRUE;
    }

    return FALSE;
  }

  /*
   * Whether the entity was transitioned from the old
   * PAR2 system on 1 October 2017.
   */
  public function isTransitioned() {
    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');

    if (isset($field_name) && $this->hasField($field_name) && !$this->get($field_name)->isEmpty() && $this->get($field_name)->getString() === 'n/a') {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Invalidate entities so that they are not transitioned to PAR3.
   */
  public function invalidate() {
    // Only three entities can be transitioned.
    if (!in_array($this->getEntityTypeId(), ['par_data_partnership', 'par_data_advice', 'par_data_inspection_plan'])) {
      return FALSE;
    }
    if (!$this->isNew() && $this->isTransitioned()) {
      // Set the status to unpublished to make filtering from display easier.
      $this->delete();

      $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');

      if (isset($field_name) && $this->hasField($field_name) && !$this->get($field_name)->isEmpty() && $this->get($field_name)->getString() === 'n/a') {
        return $this->set($field_name, 'n/a')->save();
      }
      elseif ($this->hasField('obsolete')) {
        return $this->set('obsolete', FALSE)->save();
      }
    }
    return FALSE;
  }

  /**
   * Destroy and entity, and completely remove.
   */
  public function destroy() {
    if ($this->isDeletable()) {
      parent::delete();

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Destroy entity, and completely remove.
   */
  public function annihilate() {
    return $this->entityTypeManager()->getStorage($this->entityTypeId)->destroy([$this->id() => $this]);
  }

  /**
   * Check whether this entity is destroyable.
   *
   * @TODO Related to PAR-1439, do not use until this is complete.
   */
  public function isDeletable() {
    // Only some PAR entities can be deleted.
    if (!$this->getTypeEntity()->isDeletable()) {
      return FALSE;
    }

    // If there are any relationships whereby another entity requires this one
    // then this entity should not be deleted.
    $relationships = $this->getRequiredRelationships();
    return $relationships ? FALSE : TRUE;
  }

  /**
   * Delete if this entity is deletable and is not new.
   */
  public function delete($reason = '') {
    if (!$this->isDeleted()) {
      // PAR-1507: We are moving away from soft-delete options.
      return $this->destroy();
    }

    return FALSE;
  }

  /**
   * Revoke if this entity is revokable and is not new.
   *
   * @param boolean $save
   *  Whether to save the entity after revoking.
   *
   * @param String $reason
   *  The reason this entity is being revoked.
   *
   * @return boolean
   *  True if the entity was revoked, false for all other results.
   */
  public function revoke($save = TRUE, $reason = '') {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->inProgress() && $this->getTypeEntity()->isRevokable() && !$this->isRevoked()) {
      $this->set(ParDataEntity::REVOKE_FIELD, TRUE);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      $this->set(ParDataEntity::REVOKE_REASON_FIELD, $reason);

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * Unrevoke a revoked entity.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was unrevoked, false for all other results.
   *
   */
  public function unrevoke($save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if ($this->getTypeEntity()->isRevokable() && $this->isRevoked()) {
      $this->set(ParDataEntity::REVOKE_FIELD, FALSE);
      $this->set(ParDataEntity::REVOKE_REASON_FIELD, NULL);

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * Archive if the entity is archivable and is not new.
   *
   * @param String $reason
   *   Reason for archiving this entity.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function archive($save = TRUE, $reason = '') {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->inProgress() && $this->getTypeEntity()->isArchivable() && !$this->isArchived()) {

      $this->set(ParDataEntity::ARCHIVE_FIELD, TRUE);

      // Set reason for archiving the advice.
      $this->set(ParDataEntity::ARCHIVE_REASON_FIELD, $reason);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * Restore an archived entity.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function restore($save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if ($this->getTypeEntity()->isRevokable() && $this->isArchived()) {
      $this->set(ParDataEntity::ARCHIVE_FIELD, FALSE);
      $this->set(ParDataEntity::ARCHIVE_REASON_FIELD, NULL);

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // By default there are no conditions by which an entity is frozen.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    if ($this->isDeleted() || $this->isRevoked() || $this->isArchived() || !$this->isTransitioned()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRawStatus() {
    if ($this->isDeleted()) {
      return 'deleted';
    }
    if ($this->isRevoked()) {
      return 'revoked';
    }
    if ($this->isArchived()) {
      return 'archived';
    }
    if (!$this->isTransitioned()) {
      return 'n/a';
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');

    if (isset($field_name) && $this->hasField($field_name)) {
      $status = $this->get($field_name)->getString();
    }

    return isset($status) ? $status : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getParStatus() {
    if ($this->isDeleted()) {
      return 'Deleted';
    }
    if ($this->isRevoked()) {
      return 'Revoked';
    }
    if ($this->isArchived()) {
      return 'Archived';
    }
    if (!$this->isTransitioned()) {
      return 'Not transitioned from PAR2';
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $raw_status = $this->getRawStatus();
    return $field_name && $raw_status ? $this->getTypeEntity()->getAllowedFieldlabel($field_name, $raw_status) : '';
  }

  /**
   * {@inheritdoc}
   */
  public function setParStatus($value, $ignore_transition_check = FALSE) {
    // Determine whether we can change the value based on the current status.
    if (!$this->canTransition($value) && !$ignore_transition_check) {
      // Throw exception.
      throw new ParDataException("This status transition is not allowed.");
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $allowed_values = $this->getTypeEntity()->getAllowedValues($field_name);
    if (isset($allowed_values[$value]) && $this->get($field_name)->getString() !== $value) {
      $this->set($field_name, $value);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      // Dispatch a par event.
      // @TODO We can't dispatch this event until the entity is saved, we may end up with repeat calls.
      $event = new ParDataEvent($this);
      $dispatcher = \Drupal::service('event_dispatcher');
      $dispatcher->dispatch(ParDataEvent::statusChange($this->getEntityTypeId(), $value), $event);
    }
  }

  /**
   * A helper function to get the revision at which the status was changed.
   */
  public function getStatusChanged($status) {
    // Make sure not to request this more than once for a given entity unless the status changes.
    $function_id = __FUNCTION__ . ':' . $this->uuid() . ':' . $this->getRawStatus();
    $status_revision = &drupal_static($function_id);
    if (!empty($status_revision)) {
      return $status_revision;
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');

    // Loop through all statuses and perform custom checks to
    // find the most recent change the the specified status.
    $check_status_query = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->getQuery();
    $check_status_query->allRevisions()
      ->condition('id', $this->id())
      ->sort('revision_timestamp', 'ASC');
    $results = $check_status_query->execute();

    $status_revision = NULL;
    if (!empty($results)) {
      foreach ($results as $revision_id => $r) {
        $revision = $this->entityTypeManager()->getStorage($this->getEntityTypeId())->loadRevision($revision_id);

        // If the status matches then we can save this as the status.
        if ($revision && $revision->get($field_name)->getString() === $status) {
          $status_revision = $revision;
        }
        // If we have already found an appropriate revision and the status
        // has changed then don't go any further.
        if ($status_revision && $revision->get($field_name)->getString() !== $status) {
          break;
        }
      }
    }

    return $status_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusTime($status) {
    $revision = $this->getStatusChanged($status);

    return $revision ? $revision->get('revision_timestamp')->value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusAuthor($status) {
    $revision = $this->getStatusChanged($status);

    return $revision ? $revision->get('revision_uid')->entity : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusDescription($status, $verb = 'updated') {
    $author = $this->getStatusAuthor($status);
    $time = $this->getStatusTime($status);

    // If the uid is that of the admin user this has been automatically approved.
    if ($author && $author->id() <= 1) {
      $label = "$verb automatically";
    }
    elseif ($author && $contacts = $this->getParDataManager()->getUserPeople($author)) {
      $contact = current($contacts);
      $label = "$verb by {$contact->label()}";
    }
    elseif ($author) {
      $label = "$verb by {$author->label()}";
    }

    if ($time) {
      $time_string = ' on ' . $this->getDateFormatter()->format($time, 'gds_date_format');
      $label = isset($label) ? $label . $time_string : $time_string;
    }

    return isset($label) ? ucfirst($label): NULL;
  }

  /**
   * Determine whether this entity can transition
   *
   * @return bool
   *   TRUE if transition is allowed.
   */
  public function canTransition($status) {
    $current_status = $this->getRawStatus();
    return $this->getTypeEntity()->transitionAllowed($current_status, $status);
  }

  /**
   * Get the boolean value for a field.
   *
   * @return boolean
   */
  public function getBoolean($field_name) {
    $field = $this->hasField($field_name) ? $this->get($field_name) : NULL;
    return isset($field) && !empty($field->getString()) ? TRUE : FALSE;
  }

  /**
   * Get the IDs of referenced fields.
   *
   * @return array
   */
  public function retrieveEntityIds($field_name) {
    $referencedEntities = $this->get($field_name)->referencedEntities();

    $ids = [];
    foreach ($referencedEntities as $id => $entity) {
      $ids[] = $entity->id();
    }

    return $ids;
  }

  /**
   * Get a referenced entity field as an options array.
   */
  public function getEntityFieldAsOptions($field_name) {
    if (!$this->hasField($field_name) || !$this->get($field_name) instanceof EntityReferenceFieldItemListInterface) {
      return [];
    }

    $entities = $this->get($field_name)->referencedEntities();
    return $this->getParDataManager()->getEntitiesAsOptions($entities);
  }

  /**
   * Get all the entities that are rely on this entity.
   *
   * @TODO Once PAR-1349 has been looked at this method should probably
   * be rolled into self::getRequiredRelationships()
   *
   * @return array
   *   An array of entities that are dependent on this entity, numerically indexed.
   */
  public function getDependents($dependents = []) {
    if (isset($this->dependents)) {
      foreach ($this->dependents as $entity_type) {
        $relationships = $this->getRelationships($entity_type);
        foreach ($relationships as $uuid => $relationship) {
          // Don't get yer knickers in a twist and go loopy.
          if ($relationship->getEntity()->uuid() === $this->uuid()) {
            continue;
          }

          $dependents[] = $relationship->getEntity();
          $dependents = $relationship->getEntity()->getDependents($dependents);
        }
      }
    }

    return $dependents;
  }

  /**
   * A required relationship is one that cannot be removed or broken.
   *
   * @TODO There are many complex rules to determine these relationships,
   * this needs to be worked out in greater detail in PAR-1349
   *
   * @return array|\Drupal\par_data\ParDataRelationship|\Drupal\par_data\ParDataRelationship[]
   */
  public function getRequiredRelationships() {
    // As a general rule, any entities that reference this entity
    // should stop this entity being deleted.
    $relationships = $this->getRelationships(NULL, 'dependents');
    $relationships = array_filter($relationships, function ($relationship) {
      return ($relationship->getRelationshipDirection() === ParDataRelationship::DIRECTION_REVERSE);
    });

    return $relationships;
  }

  /**
   * Get all the relationships for this entity.
   *
   * @param string $target
   *   The target type to return entities for.
   * @param string $action
   *   The action type to return relationships for.
   * @param boolean $reset
   *   Whether to reset the cache.
   *
   * @return ParDataRelationship[]
   *   An array of entities keyed by type.
   */
  public function getRelationships($target = NULL, $action = NULL, $reset = FALSE) {
    // Enable in memory caching for repeated entity lookups.
    $unique_function_id = __FUNCTION__ . ':'
      . $this->uuid() . ':'
      . (isset($target) ? $target : 'null') . ':'
      . (isset($action) ? $action : 'null');
    $relationships = &drupal_static($unique_function_id);
    if (!$reset && isset($relationships)) {
      return $relationships;
    }

    // Loading the relationships is costly so caching is necessary.
    $cache = \Drupal::cache('data')->get("par_data_relationships:{$this->uuid()}");
    if ($cache && !$reset) {
      $relationships = $cache->data;
    }
    else {
      $relationships = [];
      // Set cache tags for all these relationships.
      $tags = [$this->getEntityTypeId() . ':' . $this->id()];

      // Get all referenced entities.
      if ($this->lookupReferencesByAction($action)) {
        $references = $this->getParDataManager()
          ->getReferences($this->getEntityTypeId(), $this->bundle());

        foreach ($references as $entity_type => $fields) {
          // If the reference is on the current entity type
          // we can get the value from the current $entity.
          if ($this->getEntityTypeId() === $entity_type) {
            foreach ($fields as $field_name => $field) {
              foreach ($this->get($field_name)->referencedEntities() as $referenced_entity) {

                if (!$referenced_entity->isDeleted()) {
                  $relationship = new ParDataRelationship($this, $referenced_entity, $field);

                  // Add relationship and entity tags to cache tags.
                  $tags[] = $relationship->getEntity()
                      ->getEntityTypeId() . ':' . $relationship->getEntity()
                      ->id();
                  $relationships[$referenced_entity->uuid()] = $relationship;
                }
              }
            }
          }
          // If the reference is on another entity type
          // we must use an entity lookup to find all entities
          // that reference the current entity.
          else {
            foreach ($fields as $field_name => $field) {
              $referencing_entities = $this->getParDataManager()
                ->getEntitiesByProperty($entity_type, $field_name, $this->id());
              foreach ($referencing_entities as $referenced_entity) {

                if (!$referenced_entity->isDeleted()) {
                  $relationship = new ParDataRelationship($this, $referenced_entity, $field);

                  // Add relationship and entity tags to cache tags.
                  $tags[] = $relationship->getEntity()
                      ->getEntityTypeId() . ':' . $relationship->getEntity()
                      ->id();
                  $relationships[$referenced_entity->uuid()] = $relationship;
                }
              }
            }
          }
        }
      }

      \Drupal::cache('data')->set("par_data_relationships:{$this->uuid()}", $relationships, Cache::PERMANENT, $tags);
    }

    // Return only permitted relationships for a given action
    if ($target) {
      $relationships = array_filter($relationships, function ($relationship) use ($target) {
        return $this->filterRelationshipsByTarget($relationship, $target);
      });
    }

    // Return only permitted relationships for a given action
    if ($action) {
      $relationships = array_filter($relationships, function ($relationship) use ($action) {
        return $this->filterRelationshipsByAction($relationship, $action);
      });
    }

    return $relationships;
  }

  /**
   * Allows all relationships to be skipped.
   */
  public function lookupReferencesByAction($action = NULL) {
    // By default all references will be looked up, some entities may choose
    // to override this and skip reference checks for given actions.
    return TRUE;
  }

  /**
   * Allows relationships to be excluded based on the target entity required.
   *
   * @param $relationship
   *   The relationship to check.
   * @param $target
   *   The target entity being checked for.
   *
   * @return bool
   *   Whether not to include a given relationship.
   */
  public function filterRelationshipsByTarget($relationship, $target = NULL) {
    // By default all relationships are included, this
    // can be overridden on an entity by entity basis.
    if ($target) {
      return ($target === $relationship->getEntity()->getEntityTypeId());
    }

    return TRUE;
  }

  /**
   * Allows relationships to be excluded based on the action performed.
   *
   * @param $relationship
   *   The relationship to check.
   * @param $action
   *   The action being performed.
   *
   * @return bool
   *   Whether not to include a given relationship.
   */
  public function filterRelationshipsByAction($relationship, $action) {
    // By default all relationships are included, this
    // can be overridden on an entity by entity basis.
    switch ($action) {
      case 'manage':
        // The golden rule is that only people should relate to an authority or organisation.
        if (in_array($relationship->getEntity()->getEntityTypeId(), ['par_data_authority', 'par_data_organisation'])
          && $relationship->getBaseEntity()->getEntityTypeId() !== 'par_data_person') {
          return FALSE;
        }

        // @TODO PAR-1025: This is a temporary fix to resolve performance issues
        // with looking up the large numbers of premises.
        if ($relationship->getEntity()->getEntityTypeId() === 'par_data_premises') {
          return FALSE;
        }

    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return $this->getTypeEntity()->getRequiredFields();
  }

  /**
   * {@inheritdoc}
   */
  public function getCompletionPercentage($include_deltas = FALSE) {
    $total = 0;
    $completed = 0;

    $fields = $this->getTypeEntity()->getCompletionFields();
    foreach ($fields as $field_name) {
      if ($include_deltas) {
        // @TODO Count multiple field values individually rather than as one field.
      }
      else {
        if ($this->hasField($field_name)) {
          ++$total;
          if (!$this->get($field_name)->isEmpty() && !empty($this->get($field_name)->getString())) {
            ++$completed;
          }
        }
      }
    }

    return $total > 0 ? ($completed / $total) * 100 : 0;
  }

  /**
   * {@inheritdoc}
   */
  public function setNewRevision($value = TRUE) {
    parent::setNewRevision($value);

    if (!$this->isNew()) {
      $this->setRevisionCreationTime($this->getTime()->getRequestTime());
      $this->setRevisionAuthorId(\Drupal::currentUser()->id());
    }
  }

  /**
   * Helper function to extract field values.
   *
   * @return array
   *   An array of value properties keyed by the field delta.
   */
  public function extractValues($field, $property = 'value') {
    if (!$this->hasField($field)) {
      return;
    }

    $values = [];
    foreach ($this->get($field)->getValue() as $key => $value) {
      if (isset($value[$property])) {
        $values[$key] = $value[$property];
      }
    }

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = $fields['name']->setDefaultValueCallback(__CLASS__ . '::setDefaultTitle')
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ]);

    // We will apply action state fields to all par entities for consistency
    // but will only use certain actions on certain entities.
    $fields['deleted'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Deleted'))
      ->setDescription(t('Whether the entity has been deleted.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'hidden',
      ])
      ->setDisplayConfigurable('view', FALSE);

    $fields['revoked'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revoked'))
      ->setDescription(t('Whether the entity has been revoked.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'hidden',
      ])
      ->setDisplayConfigurable('view', FALSE);

    // Revocation Reason.
    $fields[self::REVOKE_REASON_FIELD] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Revocation Reason'))
      ->setDescription(t('Comments about why this entity was revoked.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 13,
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

    $fields['archived'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Archived'))
      ->setDescription(t('Whether the entity has been archived.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'hidden',
      ])
      ->setDisplayConfigurable('view', FALSE);

    // Archive Reason.
    $fields[self::ARCHIVE_REASON_FIELD] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Archive Reason'))
      ->setDescription(t('Comments about why this advice document was archived.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 13,
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

    // Deleted Reason.
    $fields[self::DELETE_REASON_FIELD] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Deleted Reason'))
      ->setDescription(t('Comments about why this partnership was deleted.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 13,
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
    return $fields;
  }
}
