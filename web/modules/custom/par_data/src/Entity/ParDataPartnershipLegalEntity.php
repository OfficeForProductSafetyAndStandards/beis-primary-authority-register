<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Datetime\DateTimePlus;
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

  const DATE_DISPLAY_FORMAT = 'd/m/Y';

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
   * {@inheritdoc}
   *
   * @param string $date
   *   The date this member was ceased.
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
   * Get the partnerships for this partnership legal entity.
   */
  public function getPartnership() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('field_partnership_legal_entity', $this->id())
      ->execute();

    return $this->getParDataManager()->getEntitiesByType('par_data_partnership', $query);
  }

  /**
   * Get the legal entities for this partnership legal entity.
   */
  public function getLegalEntity() {
    return $this->get('field_legal_entity')->referencedEntities();
  }

  /**
   * Add a legal entity for this partnership legal entity.
   *
   * @param ParDataLegalEntity $legal_entity
   *   A PAR Legal Entity to add.

   */
  public function addLegalEntity(ParDataLegalEntity $legal_entity) {
    // This field should only allow single values.
    $this->set('field_legal_entity', $legal_entity);
  }

  /**
   * Get the legal entity approval date.
   */
  public function getStartDate() {
    $date = !$this->get('date_legal_entity_approved')->isEmpty() ? $this->date_legal_entity_approved->date : NULL;
    return $date?->format(self::DATE_DISPLAY_FORMAT);
  }

  /**
   * Set the legal entity approval date.
   *
   * @param string $date
   *   The date this legal entity was approved.
   */
  public function setStartDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_approved', $date?->format('Y-m-d'));
  }

  /**
   * Get the legal entity revocation date.
   */
  public function getEndDate() {
    $date = !$this->get('date_legal_entity_revoked')->isEmpty() ? $this->date_legal_entity_revoked->date : NULL;
    return $date?->format(self::DATE_DISPLAY_FORMAT);
  }

  /**
   * Set the legal entity revocation date.
   *
   * @param string $date
   *   The date this legal entity was revoked.
   */
  public function setEndDate(DrupalDateTime $date = NULL) {
    $this->set('date_legal_entity_revoked', $date?->format('Y-m-d'));
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

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
