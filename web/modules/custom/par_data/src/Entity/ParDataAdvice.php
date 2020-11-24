<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;

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
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
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
  public function revoke($save = TRUE, $reason = '') {
    // Only advice of type 'authority_advice' can be revoked.
    if ($this->getRawStatus() === 'authority_advice') {
      parent::revoke($save, $reason);
    }
    else {
      $this->archive('this advice of type cannot be revoked only archived.' ,$save);
    }
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
    return $this->get('notes')->getString();
  }

  /**
   * Get the issue date for this Advice.
   */
  public function getIssueDate() {
    return $this->get('issue_date')->getString();
  }

  /**
   * Identify whether this entity has any documents.
   *
   * @return boolean
   *   Whether there are any documents.
   */
  public function hasDocuments($files = []) {
    return $this->hasField('field_document') && !$this->get('field_document')->isEmpty();
  }

  /**
   * Get the document.
   *
   * @param array $files
   */
  public function getDocuments($files = []) {
    /** @var \Drupal\media\MediaInterface[] $media */
    $media = $this->hasDocuments() ? $this->get('field_document')->referencedEntities() : [];

    foreach ($media as $id => $entity) {
      // Return documents for document media types only.
      if ($entity->bundle() === 'document' && $entity->hasField('field_media_document')) {
        $files[$id] = $entity->get('field_media_document')->getValue();
      }
    }

    return $files;
  }

  /**
   * Create a media entity based on this file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file to create a media entity for.
   * @param bool $save
   *   Whether to save the media entity (the file entity must be saved).
   */
  public function generateMedia($file, $save = TRUE) {
    // The file must be saved before adding to the media entity.
    if ($file->isTemporary()) {
      $file->setPermanent();
      $file->save();
    }

    $media = Media::create([
      'bundle'           => 'document',
      'uid'              => \Drupal::currentUser()->id(),
      'field_media_file' => [
        'target_id' => $file->id(),
      ],
    ]);

    // Save the media file.
    if ($save) {
      $media->setName($file->getFilename())->setPublished(TRUE)->save();
    }

    return $media;
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

    // Advice Notes.
    $fields['notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Advice Summary'))
      ->setDescription(t('Summary info for this advice.'))
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

    return $fields;
  }

}
