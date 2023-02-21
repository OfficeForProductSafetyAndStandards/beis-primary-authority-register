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
   * {@inheritdoc}
   */
  public function revoke($save = TRUE, $reason = '') {
    $current_date = new DrupalDateTime();
    $this->setEndDate($current_date);

    return parent::revoke($save, $reason);
  }

  /**
   * {@inheritdoc}
   */
  public function unrevoke($save = TRUE) {
    // Unset the revocation date.
    $this->setEndDate(NULL);

    return parent::unrevoke($save);
  }

  /**
   * Get the partnership for this partnership legal entity.
   *
   * @return ParDataPartnership|null
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
    return current($this->get('field_legal_entity')->referencedEntities());
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
   * Gets the legal entity approval date.
   *
   * Defaults to the partnership's approval date if
   * not explicitly set.
   *
   * @return null|DrupalDateTime
   */
  public function getStartDate($full = FALSE) {
    return !$this->get('date_legal_entity_approved')->isEmpty() ?
      $this->date_legal_entity_approved->date :
      null;
  }

  /**
   * Gets the legal entity approval date.
   *
   * Defaults to the partnership's approval date if
   * not explicitly set.
   *
   * @return null|DrupalDateTime
   */
  public function getFullStartDate() {
    $partnership_start_date = $this->getPartnership()?->getApprovedDate();
    return $this->getStartDate() ?? $partnership_start_date;
  }

  /**
   * Sets the legal entity approval date.
   *
   * @param DrupalDateTime $date
   *   The date this legal entity was approved.
   */
  public function setStartDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_approved', $date?->format('Y-m-d'));
  }

  /**
   * Gets the legal entity revocation date.
   *
   * @return null|DrupalDateTime
   */
  public function getEndDate() {
    return $this->get('date_legal_entity_revoked')->isEmpty() ? NULL : $this->date_legal_entity_revoked->date;
  }

  /**
   * Sets the legal entity revocation date.
   *
   * @param DrupalDateTime $date
   *   The date this legal entity was revoked.
   */
  public function setEndDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_revoked', $date?->format('Y-m-d'));
  }

  /**
   * Check whether the legal entity can be removed.
   *
   * Partnership Legal Entities can be removed if:
   *  - they are not attached to a partnership
   *  - the partnership they are attached to is not active
   *  - they were added within the last 24 hours
   *
   * @return bool
   *   TRUE if the legal entity can be removed.
   */
  public function isRemovable() {
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);
    $partnership = $this->getPartnership();
    return (!$partnership
        || !$partnership->isActive()
        || $this->getFullStartDate() > $now->modify('-1 day'));
  }

  /**
   * Check whether this partnership legal entity can be reinstated.
   *
   * A partnership legal entity can be reinstated if:
   *  - It has been revoked
   *  - There is not already existent another active partnership legal entity for the same legal entity.
   *
   * @return bool
   *   TRUE if the legal entity can be reinstated.
   */
  public function isReinstatable() {

    if (!$this->isRevoked()) {
      return FALSE;
    }

    foreach ($this->getPartnership()->getPartnershipLegalEntities(TRUE) as $active_partnership_le) {
      if ($this->getLegalEntity()->id() == $active_partnership_le->getLegalEntity()->id()) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Returns the internal value of the partnership_legal_entity_status base field.
   *
   * @return string
   */
  public function getPartnershipLegalEntityStatusRaw() {
    return $this->get('partnership_legal_entity_status')->getString();
  }


  /**
   * Returns the display value of the partnership_legal_entity_status base field.
   *
   * @return string
   */
  public function getPartnershipLegalEntityStatus() {
    $value = $this->getPartnershipLegalEntityStatusRaw();
    $type = !empty($value) ? $this->getTypeEntity()->getAllowedFieldlabel('partnership_legal_entity_status', $value) : NULL;
    return $type;
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

    // Partnership legal entity Status.
    $fields['partnership_legal_entity_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Partnership Legal Entity Status'))
      ->setDescription(t('The current status of the partnership legal entity itself.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'allowed_values' => [
          'awaiting_review' => 'Awaiting review',
          'confirmed_authority' => 'Confirmed by authority',
          'confirmed_business' => 'Confirmed by business',
          'confirmed_rd' => 'Active',
        ],
      ])
      ->setDefaultValue('confirmed_rd')
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'list_default',
        'weight' => 5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

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
