<?php

namespace Drupal\par_data\Entity;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_deviation_request entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_deviation_request",
 *   label = @Translation("PAR Deviation Request"),
 *   label_collection = @Translation("PAR Deviation Requests"),
 *   label_singular = @Translation("PAR Deviation Request"),
 *   label_plural = @Translation("PAR Deviation Requests"),
 *   label_count = @PluralTranslation(
 *     singular = "@count deviation request",
 *     plural = "@count deviation requests"
 *   ),
 *   bundle_label = @Translation("PAR Deviation Request type"),
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
 *   base_table = "par_deviation_requests",
 *   data_table = "par_deviation_requests_field_data",
 *   revision_table = "par_deviation_requests_revision",
 *   revision_data_table = "par_deviation_requests_field_revision",
 *   admin_permission = "administer par_data_deviation_request entities",
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
 *     "collection" = "/admin/content/par_data/par_data_deviation_request",
 *     "canonical" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}",
 *     "edit-form" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_deviation_request/{par_data_deviation_request}/delete"
 *   },
 *   bundle_entity_type = "par_data_deviation_request_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_deviation_request_t.edit_form"
 * )
 */
class ParDataDeviationRequest extends ParDataEntity {

  use ParEnforcementEntityTrait;

  const APPROVED = 'approved';
  const BLOCKED = 'blocked';

  /**
   * Check if this entity is approved.
   *
   * @return bool
   */
  public function isApproved() {
    $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $current_status = $status_field ? $this->get($status_field)->getString() : NULL;
    if ($current_status === self::APPROVED) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if this entity is revoked.
   *
   * @return bool
   */
  public function isBlocked() {
    $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $current_status = $status_field ? $this->get($status_field)->getString() : NULL;
    if ($current_status === self::BLOCKED) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if this entity is awaiting approval.
   *
   * @return bool
   */
  public function isAwaitingApproval() {
    $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $current_status = $status_field ? $this->get($status_field)->getString() : NULL;
    return ($this->getTypeEntity()->getDefaultStatus() === $current_status);
  }

  /**
   * Approve an enforcement action.
   */
  public function approve($authority_notes, $save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->isApproved()) {
      try {
        $this->setParStatus(self::APPROVED);
        $this->set('primary_authority_notes', $authority_notes);
      }
      catch (ParDataException $e) {
        // If the status could not be updated we want to log this but continue.
        $message = $this->t("This status could not be updated to '%status' for %label");
        $replacements = [
          '%label' => $this->label(),
          '%status' => self::APPROVED,
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);

        return FALSE;
      }

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }

    return FALSE;
  }

  /**
   * Revoke if this entity is revokable and is not new
   *
   * @param string $authority_notes
   *  primary authority notes submitted when the status is updated to blocked in the form.
   *
   * @return boolean
   *   True if the entity was revoked, false for all other results.
   */
  public function block($authority_notes, $save = TRUE) {
    if ($this->isNew()) {
      $save = FALSE;
    }

    if (!$this->isBlocked()) {
      try {
        $this->setParStatus(self::BLOCKED);
        $this->set('primary_authority_notes', $authority_notes);
      }
      catch (ParDataException $e) {
        // If the status could not be updated we want to log this but continue.
        $message = $this->t("This status could not be updated to '%status' for %label");
        $replacements = [
          '%label' => $this->label(),
          '%status' => self::BLOCKED,
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);

        return FALSE;
      }

      return $save ? ($this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW) : TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Freeze Enforcement Actions that are awaiting approval.
    if ($this->getTypeEntity()->getDefaultStatus() === $this->getRawStatus()) {
      return TRUE;
    }

    return parent::inProgress();
  }

  /**
   * Get the primary authority notes data from the current action.
   *
   * @return String primary_authority_notes | NULL
   *   The referred text stored on the current action or null.
   *
   */
  public function getPrimaryAuthorityNotes() {
    return $this->get('primary_authority_notes')->getString();
  }

  /**
   * Get the message comments.
   */
  public function getReplies($single = FALSE) {
    $cids = \Drupal::entityQuery('comment')
      ->condition('entity_id', $this->id())
      ->condition('entity_type', $this->getEntityTypeId())
      ->sort('cid', 'DESC')
      ->execute();
    $messages = array_values(Comment::loadMultiple($cids));
    $message = !empty($messages) ? current($messages): NULL;

    return $single ? $message : $messages;
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
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Request Date.
    $fields['request_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Request Date'))
      ->setDescription(t('The date this deviation request was issued.'))
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

    // Notes.
    $fields['notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Notes'))
      ->setDescription(t('Notes about this deviation request.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 3,
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

    // Documents.
    $fields['document'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document'))
      ->setDescription(t('Documents relating to the proposed deviation.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/deviation_request',
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

    // Primary Authority status.
    $fields['primary_authority_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Primary Authority Status'))
      ->setDescription(t('The status of the primary authority on this request.'))
      ->setRequired(TRUE)
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

    // PA notes.
    $fields['primary_authority_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Primary Authority Notes'))
      ->setDescription(t('Notes about this deviation request from the primary authority.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 6,
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

    $fields['messages'] = BaseFieldDefinition::create('comment')
      ->setLabel(t('Messages'))
      ->setDescription(t('Messages relating to this deviation request.'))
      ->setSettings(
        [
          'default_mode' => 1,
          'per_page' => 50,
          'anonymous' => 0,
          'form_location' => 1,
          'preview' => 1,
          'comment_type' => 'par_deviation_request_comments',
          'locked' => false,

      ])
      ->setDefaultValue(
        [
          'status' => 2,
          'cid' => 0,
          'last_comment_timestamp' => 0,
          'last_comment_name' => null,
          'last_comment_uid' => 0,
          'comment_count' => 0,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'comment_default',
        'settings' => [
          'form_location' => 1,
          'default_mode' => 1,
          'per_page' => 50,
          'anonymous' => 0,
          'preview' => 1,
          'comment_type' => 'par_deviation_request_comments',
          'locked' => false,

        ],
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}
