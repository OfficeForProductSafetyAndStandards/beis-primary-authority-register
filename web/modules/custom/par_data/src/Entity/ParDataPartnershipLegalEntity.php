<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\TypedData\Type\DateTimeInterface;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_partnership_legal_entity entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
   *   id = "par_data_partnership_le",
 *   label = @Translation("PAR Partnership Legal Entity"),
 *   label_collection = @Translation("PAR Partnership Legal Entities"),
 *   label_singular = @Translation("PAR Partnership Legal Entity"),
 *   label_plural = @Translation("PAR Partnership Legal Entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count partnership legal entity",
 *     plural = "@count partnership legal entities"
 *   ),
 *   bundle_label = @Translation("PAR Partnership Legal Entity Type"),
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
 *   base_table = "par_partnership_legal_entities",
 *   data_table = "par_partnership_legal_entities_field_data",
 *   revision_table = "par_partnership_legal_entities_revision",
 *   revision_data_table = "par_partnership_legal_entities_field_revision",
 *   admin_permission = "administer par_data_partnership_legal_entities entities",
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
 *     "collection" = "/admin/content/par_data/par_data_partnership_legal_entity",
 *     "canonical" = "/admin/content/par_data/par_data_partnership_legal_entity/{par_data_partnership_le}",
 *     "edit-form" = "/admin/content/par_data/par_data_partnership_legal_entity/{par_data_partnership_le}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_partnership_legal_entity/{par_data_partnership_le}/delete"
 *   },
 *   bundle_entity_type = "par_data_partnership_le_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_partnership_le_type.edit_form"
 * )
 */
class ParDataPartnershipLegalEntity extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // Partnerships should not be followed, this is a one-way relationship.
        if ($relationship->getEntity()->getEntityTypeId() === 'par_data_partnership') {
          return FALSE;
        }

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * Override parent implementation to let delete go ahead.
   *
   * @return false
   */
  public function hasDependencies() {
    parent::hasDependencies();
    return false;
  }

  /**
   * Check whether statuses are supported for this legal entity.
   *
   * Statuses are only supported for active partnerships, if the partnership
   * has any other status it is assumed that all legal entities will mirror
   * this.
   *
   * @example If a partnership is revoked, then all the legal entities will be
   * revoked.
   * @example But if a partnership is active then it is possible for a new legal
   * entity to be awaiting_approval or an old one to be revoked.
   *
   * @return bool
   *   Whether this legal entity is allowed to have its own status record.
   */
  public function supportsStatus(): bool {
    return (bool) $this->getPartnership()?->isActive();
  }

  /**
   * {@inheritdoc}
   */
  public function setParStatus($value, $ignore_transition_check = FALSE) {
    // Only set the status if allowed.
    if ($this->supportsStatus()) {
      parent::setParStatus($value, $ignore_transition_check);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRawStatus() {
    $status = parent::getRawStatus();

    // Return the status if supported, or the partnership's status.
    return $this->supportsStatus() && $status ?
      $status :
      $this->getPartnership()?->getRawStatus();
  }

  /**
   * {@inheritdoc}
   */
  public function nominate($save = TRUE) {
    // Do not nominate legal entities that are already nominated.
    if ($this->isActive()) {
      return FALSE;
    }

    // Do not nominate if status isn't supported.
    if (!$this->supportsStatus()) {
      return FALSE;
    }

    // Set the status to active.
    $this->setParStatus('confirmed_rd');

    // Set the approved date.
    $current_date = new DrupalDateTime();
    $this->setStartDate($current_date);

    // Ensure that nominating a revoked partnership succeeds.
    $this->unrevoke(FALSE);

    return !$save || $this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW;
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($save = TRUE, $reason = '') {
    // Do not revoke legal entities that are already revoked.
    if ($this->isRevoked()) {
      return FALSE;
    }

    // Do not revoke if status isn't supported.
    if (!$this->supportsStatus()) {
      return FALSE;
    }

    // Set the revocation date.
    $current_date = new DrupalDateTime();
    $this->setEndDate($current_date);

    // Ensure the approved date is re-set.
    $this->setStartDate(NULL);

    return parent::revoke($save, $reason);
  }

  /**
   * {@inheritdoc}
   */
  public function unrevoke($save = TRUE) {
    // Only restore legal entities that are revoked.
    if (!$this->isRevoked()) {
      return FALSE;
    }

    // Do not unrevoke if status isn't supported.
    if (!$this->supportsStatus()) {
      return FALSE;
    }

    // Unset the revocation date.
    $this->setEndDate(NULL);

    $restored = parent::unrevoke(FALSE);

    // Re-nominate if the original status was nominated.
    $this->nominate(FALSE);

    // Only save the changes if the legal entity was changed.
    if ($restored) {
      return !$save || $this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    // Whether an entity is complete and can be acted upon as a finished, live.
    $awaiting_statuses = [
      $this->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'confirmed_business'
    ];

    if (in_array($this->getRawStatus(), $awaiting_statuses)) {
      return FALSE;
    }

    // If there is no start date.
    if (!$this->getStartDate()) {
      return FALSE;
    }

    return parent::isActive();
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Freeze partnerships that are awaiting approval.
    $awaiting_statuses = [
      $this->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'confirmed_business'
    ];

    if (in_array($this->getRawStatus(), $awaiting_statuses)) {
      return TRUE;
    }

    // If there is no start date.
    if (!$this->getStartDate()) {
      return TRUE;
    }

    return parent::inProgress();
  }

  /**
   * Get the partnership for this partnership legal entity.
   *
   * @return ?ParDataPartnership
   */
  public function getPartnership() {
    // Make sure not to request this more than once for a given entity.
    $partnership = &drupal_static(__FUNCTION__ . ':' . $this->uuid());
    if (isset($partnership)) {
      return $partnership;
    }

    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('field_partnership_legal_entity', $this->id())
      ->execute();

    $partnerships = $this->getParDataManager()->getEntitiesByType('par_data_partnership', $query);
    return !empty($partnerships) ? reset($partnerships) : NULL;
  }

  /**
   * Get the legal entity for this partnership legal entity.
   *
   * @return ParDataLegalEntity
   */
  public function getLegalEntity() {
    return !$this->get('field_legal_entity')->isEmpty() ?
      current($this->get('field_legal_entity')->referencedEntities()) :
      NULL;
  }

  /**
   * Set the legal entity for this partnership legal entity.
   *
   * @param ParDataLegalEntity $legal_entity
   *   A PAR Legal Entity to set.

   */
  public function setLegalEntity(ParDataLegalEntity $legal_entity) {
    // This field should only allow single values.
    $this->set('field_legal_entity', $legal_entity);
  }

  /**
   * Gets the raw legal entity approval date.
   *
   * Only includes the date set on this legal entity.
   *
   * @return ?DrupalDateTime
   */
  public function getRawStartDate(): ?DrupalDateTime {
    return !$this->get('date_legal_entity_approved')->isEmpty() ?
      $this->date_legal_entity_approved->date :
      NULL;
  }

  /**
   * Gets the legal entity approval date.
   *
   * Defaults to the partnership's approval date if
   * not explicitly set.
   *
   * @return ?DrupalDateTime
   */
  public function getStartDate(): ?DrupalDateTime {

    return $this->getRawStartDate() ?? $this->getPartnership()?->getApprovedDate();
  }

  /**
   * Sets the legal entity approval date.
   *
   * @param ?DrupalDateTime $date
   *   The date this legal entity was approved.
   */
  public function setStartDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_approved', $date?->format('Y-m-d'));
  }

  /**
   * Gets the raw legal entity end date.
   *
   * Only includes the date set on this legal entity.
   *
   * @return ?DrupalDateTime
   */
  public function getRawEndDate(): ?DrupalDateTime {
    return !$this->get('date_legal_entity_revoked')->isEmpty() ?
      $this->date_legal_entity_revoked->date :
      NULL;
  }

  /**
   * Gets the legal entity end date.
   *
   * If none is set, and the partnership is revoked, use the partnership revocation date.
   *
   * @return ?DrupalDateTime
   */
  public function getEndDate(): ?DrupalDateTime {
    // If the partnerships is revoked, the revocation date can be used.
    $partnership_revocation_date = $this->getPartnership()?->isRevoked() ?
      $this->getPartnership()?->getRevocationDate() : NULL;

    return $this->getRawEndDate() ?? $partnership_revocation_date;

  }

  /**
   * Sets the legal entity revocation date.
   *
   * @param ?DrupalDateTime $date
   *   The date this legal entity was revoked.
   */
  public function setEndDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_revoked', $date?->format('Y-m-d'));
  }

  /**
   * Check whether the legal entity can be removed.
   *
   * Partnership Legal Entities can be removed if any of these are true:
   *  - they are not attached to a partnership
   *  - the partnership they are attached to is not active
   *  - they were added within the last 24 hours
   *
   * @return bool
   *   TRUE if the legal entity can be removed.
   */
  public function isRemovable() {
    $partnership = $this->getPartnership();

    // If there's no partnership, or the partnership is not active.
    if (!$partnership || !$partnership->isActive()) {
      return TRUE;
    }

    // If the legal entity is pending.
    if ($this->inProgress()) {
      return TRUE;
    }

    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);
    // If there's no start date, or the start date is less than 1 day ago.
    if ($this->getStartDate() > $now->modify('-1 day')) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check whether this partnership legal entity can be reinstated.
   *
   * A partnership legal entity can be reinstated if ALL of these are true:
   *  - It has been revoked
   *  - There is not already existent another active partnership legal entity for the same legal entity.
   *
   * @return bool
   *   TRUE if the legal entity can be reinstated.
   */
  public function isReinstatable() {
    // If the legal entity isn't revoked.
    if (!$this->isRevoked() || !$this->getEndDate()) {
      return FALSE;
    }

    // If this legal entity was revoked more than 1 day ago.
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);
    // If there's no start date, or the start date is less than 1 day ago.
    if ($this->getEndDate() < $now->modify('-1 day')) {
      return FALSE;
    }

    // If this legal entity id is already active on the partnership.
    foreach ($this->getPartnership()->getPartnershipLegalEntities(TRUE) as $active_partnership_le) {
      if ($this->getLegalEntity()->id() == $active_partnership_le->getLegalEntity()->id()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Test whether the partnership_legal_entity is active during a given period.
   *
   * @param DrupalDateTime | NULL $period_from
   *   The start date of the period to be compared.
   * @param DrupalDateTime | NULL $period_to
   *   The end date of the period to be compared.
   *
   * @return bool
   *   TRUE if the given period overlaps with the active period of the PLE.
   */
  public function isActiveDuringPeriod(DrupalDateTime $period_from = NULL, DrupalDateTime $period_to = NULL) {
    /**
     * Annoyingly DrupalDateTime objects have no comparison method, so we use DateTime objects representing the periods
     * because these are easy to compare.
     * If the 'from' date of a period is NULL this means the period starts at the date the partnership became active. We
     * could look this up, but that would be costly, and setting the date to a day in the far past has the same effect.
     * If the 'to' date is NULL then the period extends indefinitely into the future. We use a day in the far future
     * in this case.
     */

    // Create DateTime objects defining the period we are comparing to this PLE object's active period.
    $compare_from = (!$period_from) ? new \DateTime('0000-01-01 12:00:00') : $period_from->getPhpDateTime();
    $compare_to = (!$period_to) ? new \DateTime('9999-12-31 12:00:00') : $period_from->getPhpDateTime();

    // Create DateTime objects defining this PLE object's active period.
    $ple_from = (!$this->getStartDate()) ? new \DateTime('0001-01-01 12:00:00') : $this->getStartDate()->getPhpDateTime();
    $ple_to = (!$this->getEndDate()) ? new \DateTime('9999-12-31 12:00:00') : $this->getEndDate()->getPhpDateTime();

    // If the start of either period is inside the other period then the periods overlap.
    if ($ple_from <= $compare_from && $compare_from <= $ple_to) {
      return TRUE;
    }
    if ($compare_from <= $ple_from && $ple_from <= $compare_to) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Partnership legal entity status.
    $fields['legal_entity_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Legal Entity Status'))
      ->setDescription(t('The current status of the legal entity on the partnership. For example, active, revoked.'))
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
        'region' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership legal entity approval date.
    $fields['date_legal_entity_approved'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Approval Date'))
      ->setDescription(t('The date this legal entity was approved.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'region' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership legal entity revocation date.
    $fields['date_legal_entity_revoked'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Revocation Date'))
      ->setDescription(t('The date this legal entity was revoked.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'region' => 'hidden',
        'weight' => 4,
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
