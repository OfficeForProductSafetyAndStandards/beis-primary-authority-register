<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\trance\Trance;

/**
 * Defines the PAR entities.
 *
 * @ingroup par_data
 */
class ParDataEntity extends Trance implements ParDataEntityInterface {

  const DELETE_FIELD = 'deleted';
  const REVOKE_FIELD = 'revoked';
  const ARCHIVE_FIELD = 'archived';

  /**
   * Simple getter to inject the PAR Data Manager service.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
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
    $label_fields = $this->getTypeEntity()->getConfigurationElementByType('entity', 'label_fields');
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
        return current($this->get($field_name)->referencedEntities())->label();
      }
      elseif (!empty($property_name)) {
        return current($this->get($field_name)->getValue())[$property_name];
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
    if (!$this->isNew()) {
      return $this->entityManager()->getStorage($this->entityTypeId)->destroy([$this->id() => $this]);
    }
  }

  /**
   * Delete if this entity is deletable and is not new.
   */
  public function delete() {
    if (!$this->isNew() && !$this->inProgress() && $this->getTypeEntity()->isDeletable() && !$this->isDeleted()) {
      // Set the status to unpublished to make filtering from display easier.
      $this->set('status', 0);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      return parent::delete();
    }
  }

  /**
   * Revoke if this entity is revokable and is not new.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was revoked, false for all other results.
   */
  public function revoke($save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->inProgress() && $this->getTypeEntity()->isRevokable() && !$this->isRevoked()) {
      $this->set(ParDataEntity::REVOKE_FIELD, TRUE);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      return $save ? ($this->save() === SAVED_UPDATED) : TRUE;
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

      return $save ? ($this->save() === SAVED_UPDATED) : TRUE;
    }
    return FALSE;
  }

  /**
   * Archive if the entity is archivable and is not new.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function archive($save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->inProgress() && $this->getTypeEntity()->isArchivable() && !$this->isArchived()) {
      $this->set(ParDataEntity::ARCHIVE_FIELD, TRUE);

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      return $save ? ($this->save() === SAVED_UPDATED) : TRUE;
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

      return $save ? ($this->save() === SAVED_UPDATED) : TRUE;
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
  public function setParStatus($value) {
    // Determine whether we can change the value based on the current status.
    if (!$this->canTransition($value)) {
      // Throw exception.
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $allowed_values = $this->getTypeEntity()->getAllowedValues($field_name);
    if (isset($allowed_values[$value])) {
      $this->set($field_name, $value);

      // Always revision status changes.
      $this->setNewRevision(TRUE);
    }
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
   * Get all the entities that are dependent on this entity.
   *
   * @return array
   *   An array of entities that are dependent on this entity, numerically indexed.
   */
  public function getDependents($dependents = []) {
    if (!isset($this->dependents) || empty($this->dependents)) {
      return $dependents;
    }

    foreach ($this->dependents as $entity_type) {
      $relationships = $this->getRelationships($entity_type);
      foreach ($relationships as $entity) {
        // Don't get yer knickers in a twist and go loopy.
        if ($entity->uuid() === $this->uuid()) {
          continue;
        }

        $dependents[] = $entity;
        $dependents = $entity->getDependents($dependents);
      }
    }

    return $dependents;
  }

  /**
   * Get all the relationships for this entity.
   *
   * @param string $target
   *   The target type to return entities for.
   *
   * @return EntityInterface[]
   *   An array of entities keyed by type.
   */
  public function getRelationships($target = NULL) {
    $entities = [];

    // Get all referenced entities.
    $references = $this->getParDataManager()->getReferences($this->getEntityTypeId(), $this->bundle());
    foreach ($references as $entity_type => $fields) {
      // If the reference is on the current entity type
      // we can get the value from the current $entity.
      if ($this->getEntityTypeId() === $entity_type) {
        foreach ($fields as $field_name => $field) {
          foreach ($this->get($field_name)->referencedEntities() as $referenced_entity) {
            $entities[$referenced_entity->getEntityTypeId()][$referenced_entity->id()] = $referenced_entity;
          }
        }
      }
      // If the reference is on another entity type
      // we must use an entity lookup to find all entities
      // that reference the current entity.
      else {
        foreach ($fields as $field_name => $field) {
          $referencing_entities = $this->getParDataManager()->getEntitiesByProperty($entity_type, $field_name, $this->id());
          if ($referencing_entities) {
            if (!isset($entities[$entity_type])) {
              $entities[$entity_type] = [];
            }
            $entities[$entity_type] += $referencing_entities;
          }
        }
      }
    }

    if ($target) {
      return isset($entities[$target]) ? array_filter($entities[$target]) : [];
    }
    else {
      return $entities;
    }
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
    $this->setRevisionCreationTime(REQUEST_TIME);
    $this->setRevisionAuthorId(\Drupal::currentUser()->id());

    parent::setNewRevision($value);
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

    return $fields;
  }

}
