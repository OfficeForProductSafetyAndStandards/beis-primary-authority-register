<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_coodinated_business entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_coordinated_business",
 *   label = @Translation("PAR Coordinated Business"),
 *   label_collection = @Translation("PAR Coordinated Businesses"),
 *   label_singular = @Translation("PAR Coordinated Business"),
 *   label_plural = @Translation("PAR Coordinated Businesses"),
 *   label_count = @PluralTranslation(
 *     singular = "@count coordinated business",
 *     plural = "@count coordinated businesses"
 *   ),
 *   bundle_label = @Translation("PAR Coordinated Business type"),
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
 *   base_table = "par_coordinated_businesses",
 *   data_table = "par_coordinated_businesses_field_data",
 *   revision_table = "par_coordinated_businesses_revision",
 *   revision_data_table = "par_coordinated_businesses_field_revision",
 *   admin_permission = "administer par_data_coordinated_business entities",
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
 *     "collection" = "/admin/content/par_data/par_data_coordinated_business",
 *     "canonical" = "/admin/content/par_data/par_data_coordinated_business/{par_data_coordinated_business}",
 *     "edit-form" = "/admin/content/par_data/par_data_coordinated_business/{par_data_coordinated_business}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_coordinated_business/{par_data_coordinated_business}/delete"
 *   },
 *   bundle_entity_type = "par_data_coordinated_business_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_coordinated_business_t.edit_form"
 * )
 */
class ParDataCoordinatedBusiness extends ParDataEntity {

  /**
   * Get the time service.
   */
  public function getTime() {
    return \Drupal::time();
  }

  /**
   * {@inheritdoc}
   *
   * @param string $date
   *   The date this member was ceased.
   */
  public function cease($date = '', $save = TRUE) {
    if (!empty($date)) {
      $this->set('date_membership_ceased', $date);
    }

    // Ceasing a member has the same purpose as revoking partnerships
    // so we use the same methods and status.
    // Coordinated members that are active will also be revoked
    parent::revoke($save, TRUE);
  }

  /**
   * Destroy and entity, and completely remove.
   */
  public function destroy() {
    if (!$this->isNew()) {
      if (!$this->inProgress()) {
        $this->entityManager()->getStorage($this->entityTypeId)->destroy([$this->id() => $this]);
      }
      else {
        $date = DateTimePlus::createFromFormat('Y-m-d', 'now');
        $this->cease($date, TRUE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Freeze memberships that have active enforcement notices.
    $enforcement_notices = $this->getRelationships('par_data_enforcement_notice');
    foreach ($enforcement_notices as $enforcement_notice) {
      if ($enforcement_notice->inLiving()) {
        return TRUE;
      }
    }

    return parent::inProgress();
  }

  /**
   * Get the contacts for this Coordinated Business.
   */
  public function getPerson() {
    return $this->get('field_person')->referencedEntities();
  }

  /**
   * Get the partnerships for this Coordinated Business.
   */
  public function getPartnership() {
    $query = $this->getParDataManager()->getEntityQuery('par_data_partnership')
      ->condition('field_coordinated_business', $this->id())
      ->execute();

    return $this->getParDataManager()->getEntitiesByType('par_data_partnership', $query);
  }

  /**
   * Get the legal entites for this Coordinated Business.
   */
  public function getLegalEntity() {
    return $this->get('field_legal_entity')->referencedEntities();
  }

  /**
   * Get the legal entites for this Coordinated Business.
   */
  public function getOrganisation($single = FALSE) {
    $organisations = $this->get('field_organisation')->referencedEntities();
    $organisation = !empty($organisations) ? current($organisations) : NULL;

    return $single ? $organisation : $organisations;
  }

  /**
   * Add a legal entity for this Coordinated Business.
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
   * Get the premises for this Coordinated Business.
   */
  public function getPremises() {
    return $this->get('field_premises')->referencedEntities();
  }

  /**
   * Get the SIC Code for this Coordinated Business.
   */
  public function getSicCode() {
    return $this->get('field_sic_code')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Membership Date.
    $fields['membership_date'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Membership Date'))
      ->setDescription(t('The date range this coordinated business is a member of this partnership for.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership info confirmed by business.
    $fields['covered_by_inspection'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Covered by inspection plan'))
      ->setDescription(t('Is this coordinated business covered by inspection plan?'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Coordinated business membership start date.
    $fields['date_membership_began'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Membership Start Date'))
      ->setDescription(t('The date the membership began.'))
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

    // Coordinated business cease date.
    $fields['date_membership_ceased'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Membership Ceased Date'))
      ->setDescription(t('The date this membership was ceased.'))
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
