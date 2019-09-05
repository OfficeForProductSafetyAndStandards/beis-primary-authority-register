<?php

namespace Drupal\par_data\Entity;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_general_enquiry entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_general_enquiry",
 *   label = @Translation("PAR General Enquiry"),
 *   label_collection = @Translation("PAR General Enquiries"),
 *   label_singular = @Translation("PAR General Enquiry"),
 *   label_plural = @Translation("PAR General Enquiries"),
 *   label_count = @PluralTranslation(
 *     singular = "@count general_enquiry",
 *     plural = "@count general_enquiries"
 *   ),
 *   bundle_label = @Translation("PAR General Enquiry type"),
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
 *   base_table = "par_general_enquiry",
 *   data_table = "par_general_enquiry_field_data",
 *   revision_table = "par_general_enquiry_revision",
 *   revision_data_table = "par_general_enquiry_field_revision",
 *   admin_permission = "administer par_data_general_enquiry entities",
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
 *     "collection" = "/admin/content/par_data/par_data_general_enquiry",
 *     "canonical" = "/admin/content/par_data/par_data_general_enquiry/{par_data_general_enquiry}",
 *     "edit-form" = "/admin/content/par_data/par_data_general_enquiry/{par_data_general_enquiry}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_general_enquiry/{par_data_general_enquiry}/delete"
 *   },
 *   bundle_entity_type = "par_data_general_enquiry_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_general_enquiry_t.edit_form"
 * )
 */
class ParDataGeneralEnquiry extends ParDataEntity {

  use ParEnforcementEntityTrait;

  /**
   * Get the message comments.
   */
  public function getReplies($single = FALSE) {
    $cids = \Drupal::entityQuery('comment')
      ->condition('entity_id', $this->id())
      ->condition('entity_type', $this->getEntityTypeId())
      ->sort('cid', 'DESC')
      ->execute();
    $messages = !empty($cids) ? array_values(Comment::loadMultiple($cids)) : NULL;
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
      ->setDescription(t('The date this General Enquiry was issued.'))
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
      ->setDescription(t('Notes about this General Enquiry.'))
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
      ->setDescription(t('Supporting documents.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/general_enquiry',
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
      ->setDescription(t('Notes about this General Enquiry from the primary authority.'))
      ->addConstraint('par_required')
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
      ->setDescription(t('Replies to this general enquiry.'))
      ->setSettings(
        [
          'default_mode' => 1,
          'per_page' => 50,
          'anonymous' => 0,
          'form_location' => 1,
          'preview' => 1,
          'comment_type' => 'par_general_enquiry_comments',
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
          'comment_type' => 'par_general_enquiry_comments',
          'locked' => false,

        ],
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
