<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_inspection_plan entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_inspection_plan",
 *   label = @Translation("PAR Inspection Plan"),
 *   label_collection = @Translation("PAR Inspection Plans"),
 *   label_singular = @Translation("PAR Inspection Plan"),
 *   label_plural = @Translation("PAR Inspection Plans"),
 *   label_count = @PluralTranslation(
 *     singular = "@count inspection plan",
 *     plural = "@count inspection plans"
 *   ),
 *   bundle_label = @Translation("PAR Inspection Plan type"),
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
 *   base_table = "par_inspection_plans",
 *   data_table = "par_inspection_plans_field_data",
 *   revision_table = "par_inspection_plans_revision",
 *   revision_data_table = "par_inspection_plans_field_revision",
 *   admin_permission = "administer par_data_inspection_plan entities",
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
 *     "collection" = "/admin/content/par_data/par_data_inspection_plan",
 *     "canonical" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}",
 *     "edit-form" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_inspection_plan/{par_data_inspection_plan}/delete"
 *   },
 *   bundle_entity_type = "par_data_inspection_plan_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_inspection_plan_type.edit_form"
 * )
 */
class ParDataInspectionPlan extends ParDataEntity {

  /**
   * Get PAR inspection plan's title.
   *
   * @return string
   *   inspection plan entity title.
   */
  public function getTitle() {
    return $this->get('title')->getString();
  }

  /**
   * Get PAR inspection plan's summary.
   *
   * @return string
   *   inspection plan entity summary.
   */
  public function getSummary() {
    return $this->get('summary')->getString();
  }

  /**
   * Set PAR inspection plan's title.
   *
   * @param string $title
   */
  public function setTitle($title) {
    $this->set('title', $title);
  }

  /**
   * Set PAR inspection plan's summary.
   */
  public function setSummary($summary) {
    $this->set('summary', $summary);
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
   * Revoke if this entity is revokable and is not new.
   *
   *  @param boolean $save
   *   Whether to save the entity after revoking.
   *
   *  @param String $reason
   *   The reason this entity is being revoked.
   *
   * @return boolean
   *   True if the entity was revoked, false for all other results.
   */
  public function revoke($save = TRUE, $reason = '') {

    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->inProgress() && $this->getTypeEntity()->isRevokable() && !$this->isRevoked()) {

      $this->set(ParDataEntity::REVOKE_FIELD, TRUE);

      // Set this inspection plans status to expired.
      try {
        $this->setParStatus('expired');
      }
      catch (ParDataException $exception) {

      }

      // Set revoke reason.
      $this->set(ParDataEntity::REVOKE_REASON_FIELD, $reason);

      // If the inspection plan is being revoked as a the result of a partnership revocation
      // keep the original revoke date so that the inspection plan can be restored later.
      if ($reason !== ParDataPartnership::INSPECTION_PLAN_REVOKE_REASON) {
        // In case a revoke timestamp needs to be applied to an entity date value.
        $this->setRevokeDateTimestamp();
      }

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function unrevoke($save = TRUE) {
    $revoke_time_stamp = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
    $revoke_time_stamp_value = $revoke_time_stamp->format("Y-m-d");

    // Only restore inspection plans that have not expired.
    if ($this->get('valid_date')->end_value > $revoke_time_stamp_value) {
      parent::unrevoke($save);
    }
  }

  /**
   * Helper function for entities that need to update a date value to be inline with a revoke timestamp.
   */
  public function setRevokeDateTimestamp() {
    $revoke_time_stamp = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
    $revoke_time_stamp_value = $revoke_time_stamp->format("Y-m-d");
    $this->set('valid_date', ['value' => $this->get('valid_date')->value, 'end_value' => $revoke_time_stamp_value]);
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Inspection plan title.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Inspection plan title'))
      ->setDescription(t('The title of the inspection plan.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
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

    // Inspection plan summary.
    $fields['summary'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Inspection plan summary'))
      ->setDescription(t('Summary info for this inspection plan.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 2,
        'settings' => [
          'rows' => 25,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Valid Date.
    $fields['valid_date'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Valid Date'))
      ->setDescription(t('The date range this inspection plan is valid for.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setRequired(TRUE)
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

    // Approved RD Executive.
    $fields['approved_rd_executive'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Approved by RD Executive'))
      ->setDescription(t('Whether this inspection plan has been approved by an RD executive.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
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

    // Approved RD Executive.
    $fields['consulted_national_regulator'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('National Regulator Consulted'))
      ->setDescription(t('Whether the national regulator has been consulted about this inspection plan.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Documents.
    $fields['document'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document'))
      ->setDescription(t('Documents relating to the inspection.'))
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/inspection_plan',
      ])
      ->setDisplayOptions('form', [
        'weight' => 4,
        'default_widget' => "file_generic",
        'default_formatter' => "file_default",
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Inspection Status.
    $fields['inspection_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Inspection Status'))
      ->setDescription(t('The current status of the inspection plan itself. For example, current, expired, replaced.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 5,
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
