<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_advice entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_advice",
 *   label = @Translation("PAR Advice"),
 *   label_collection = @Translation("PAR Advice"),
 *   label_singular = @Translation("PAR Advice"),
 *   label_plural = @Translation("PAR Advice"),
 *   label_count = @PluralTranslation(
 *     singular = "@count advice",
 *     plural = "@count advices"
 *   ),
 *   bundle_label = @Translation("PAR Advice type"),
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
 *   base_table = "par_advice",
 *   data_table = "par_advice_field_data",
 *   revision_table = "par_advice_revision",
 *   revision_data_table = "par_advice_field_revision",
 *   admin_permission = "administer par_data_advice entities",
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
 *     "collection" = "/admin/content/par_data/par_data_advice",
 *     "canonical" = "/admin/content/par_data/par_data_advice/{par_data_advice}",
 *     "edit-form" = "/admin/content/par_data/par_data_advice/{par_data_advice}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_advice/{par_data_advice}/delete"
 *   },
 *   bundle_entity_type = "par_data_advice_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_advice_type.edit_form"
 * )
 */
class ParDataAdvice extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // No relationships should be followed, this is one of the lowest tier entities.
        return FALSE;

    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($save = TRUE) {
    // Only advice of type 'authority_advice' can be revoked.
    if ($this->getRawStatus() === 'authority_advice') {
      parent::revoke($save);
    }
    else {
      $this->archive($save);
    }
  }

  /**
   * Archive if the entity is archivable and is not new.
   * Override main base class function to add custom advice entity logic.
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

      // Set the core par_entity field representing a par entity being archived.
      $this->set(ParDataEntity::ARCHIVE_FIELD, TRUE);

      // Set the advice status field.
      $this->set('advice_status', 'archived');

      // Always revision status changes.
      $this->setNewRevision(TRUE);

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }
    return FALSE;
  }

  /**
   * Get the regulatory functions for this Advice.
   */
  public function getRegulatoryFunction() {
    return $this->get('field_regulatory_function')->referencedEntities();
  }

  /**
   * Get PAR Advice's title.
   *
   * @return string
   *   advice entity title.
   */
  public function getAdviceTitle() {
    return $this->get('advice_title')->getString();
  }

  /**
   * Set PAR Advice's title.
   */
  public function setAdviceTitle($advice_title) {
    $this->set('advice_title', $advice_title);
  }

  /**
   * Get PAR Advice type.
   *
   * @return string
   *   advice entity title.
   */
  public function getAdviceType() {
    return $this->get('advice_type')->getString();
  }

  /**
   * Get PAR Advice summary.
   *
   * @return string
   *   advice entity title.
   */
  public function getAdviceSummary() {
    return $this->get('advice_summary')->getString();
  }

  /**
   * Get the issue date for this Advice.
   */
  public function getIssueDate() {
    return $this->get('issue_date')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Advice Title.
    $fields['advice_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Advice Title'))
      ->setDescription(t('The title of the advice documents.'))
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

    // Advice Type.
    $fields['advice_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Advice Type'))
      ->setDescription(t('The type of advice.'))
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

    // Advice Summary.
    $fields['advice_summary'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Advice Summary'))
      ->setDescription(t('Summary info for this advice.'))
      ->setRequired(TRUE)
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

    // Authority Visible.
    $fields['visible_authority'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Authority'))
      ->setDescription(t('Whether this advice is visible to an authority.'))
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

    // Coordinator Visible.
    $fields['visible_coordinator'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Co-ordinator'))
      ->setDescription(t('Whether this advice is visible to a co-ordinator.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Business Visible.
    $fields['visible_business'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Visible to Business'))
      ->setDescription(t('Whether this advice is visible to a business.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Issue Date.
    $fields['issue_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Issue Date'))
      ->setDescription(t('The date this advice was issued.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 2,
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
      ->setDescription(t('Documents relating to the advice.'))
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/advice',
      ])
      ->setDisplayOptions('form', [
        'weight' => 6,
        'default_widget' => "file_generic",
        'default_formatter' => "file_default",
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Advice Status.
    $fields['advice_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Advice Status'))
      ->setDescription(t('The current status of the advice. For example, active, archived.'))
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);
    // Archive Reason.
    $fields['archive_reason'] = BaseFieldDefinition::create('text_long')
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
    return $fields;
  }

}
