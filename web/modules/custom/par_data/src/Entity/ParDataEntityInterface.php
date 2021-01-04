<?php

namespace Drupal\par_data\Entity;

use Drupal\trance\TranceInterface;

/**
 * The interface for PAR entities.
 *
 * @ingroup par_data
 */
interface ParDataEntityInterface extends TranceInterface {

  /**
   * Get the view builder for the entity.
   *
   * @return \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  public function getViewBuilder();

  /**
   * Return the stored value of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getRawStatus();

  /**
   * Return the label of the status field.
   *
   * @return NULL|mixed
   *   The value of the status field.
   */
  public function getParStatus();

  /**
   * Set the status field value.
   *
   * @parap mixed
   *   The value of the status to set.
   */
  public function setParStatus($value);

  /**
   * Any entity that is in a live, active state.
   *
   * Only finished and completed entities count, this is typically useful for
   * entities that go through an approval process.
   *
   * @see Issue PAR-1402
   *
   * @return bool
   *   TRUE if entity is active.
   */
  public function isActive();

  /**
   * Any entity that is in progress can't be revoked, archived or deleted.
   *
   * @return bool
   *   TRUE if entity is in progress.
   */
  public function inProgress();

  /**
   * Whether this entity is revoked.
   *
   * @return bool
   *   TRUE if entity has been revoked.
   */
  public function isRevoked();

  /**
   * Whether this entity is archived.
   *
   * @return bool
   *   TRUE if entity has been archived.
   */
  public function isArchived();

  /**
   * Check whether an entity can be deleted. Will first check whether the entity
   * type is protected followed by checks to make sure this entity doesn't
   * have any protected relationships to existing entities that would require it
   * to remain in the system.
   *
   * @return bool
   *   TRUE if entity can be deleted.
   */
  public function isDeletable();

  /**
   * Delete if this entity is deletable and is not new.
   *
   * @param string $reason
   *   The reason for deleting this entity.
   *
   * @return bool
   *   TRUE if the entity can be deleted
   *   FALSE if the entity cannot be deleted, or it has already been removed.
   */
  public function delete($reason = '');

  /**
   * Destroy and entity, and completely remove from the system without checking
   * whether the entity can be deleted. Use with caution.
   */
  public function annihilate();

  /**
   * Revoke if this entity is revokable and is not new.
   *
   * @param boolean $save
   *  Whether to save the entity after revoking.
   * @param String $reason
   *  The reason this entity is being revoked.
   *
   * @return boolean
   *  TRUE if the entity was revoked, FALSE for all other results.
   */
  public function revoke($save = TRUE, $reason = '');

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
  public function unrevoke($save = TRUE);

  /**
   * Archive if the entity is archivable and is not new.
   *
   * @param String $reason
   *   Reason for archiving this entity.
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function archive($save = TRUE, $reason = '');

  /**
   * Restore an archived entity.
   *
   * @param boolean $save
   *   Whether to save the entity after revoking.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function restore($save = TRUE);

  /**
   * Get the level of completion of this entity.
   *
   * @return NULL|integer
   *   The percentage completion value.
   */
  public function getCompletionPercentage();

  /**
   * Runs the plain_text filter on a piece of text used to format long_text fields correctly.
   *
   * @param string $field
   *   The name of the field on the entity to act on.
   *
   * @return \Drupal\Component\Render\MarkupInterface/NULL
   *   The filtered text
   */
  public function getPlain($field);
}
