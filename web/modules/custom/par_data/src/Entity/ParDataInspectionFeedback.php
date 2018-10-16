<?php

namespace Drupal\par_data\Entity;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the par_data_inspection_feedback entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_inspection_feedback",
 *   label = @Translation("PAR Inspection Feedback"),
 *   label_collection = @Translation("PAR Inspection Feedbacks"),
 *   label_singular = @Translation("PAR Inspection Feedback"),
 *   label_plural = @Translation("PAR Inspection Feedbacks"),
 *   label_count = @PluralTranslation(
 *     singular = "@count inspection_feedback",
 *     plural = "@count inspection_feedbacks"
 *   ),
 *   bundle_label = @Translation("PAR Inspection Feedback type"),
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
 *   base_table = "par_inspection_plan_feedback",
 *   data_table = "par_inspection_plan_feedback_field_data",
 *   revision_table = "par_inspection_plan_feedback_revision",
 *   revision_data_table = "par_inspection_plan_feedback_field_revision",
 *   admin_permission = "administer par_data_inspection_feedback entities",
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
 *     "collection" = "/admin/content/par_data/par_data_inspection_feedback",
 *     "canonical" = "/admin/content/par_data/par_data_inspection_feedback/{par_data_inspection_feedback}",
 *     "edit-form" = "/admin/content/par_data/par_data_inspection_feedback/{par_data_inspection_feedback}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_inspection_feedback/{par_data_inspection_feedback}/delete"
 *   },
 *   bundle_entity_type = "par_data_inspection_feedback_t",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_inspection_feedback_t.edit_form"
 * )
 */
class ParDataInspectionFeedback extends ParDataEntity {

  use ParEnforcementEntityTrait;

  /**
   * Get the primary authority for this Deviation Request.
   *
   * @param boolean $single
   *
   * @return ParDataEntityInterface|bool
   *   Return false if not referred.
   *
   */
  public function getPrimaryAuthority($single = FALSE) {
    if ($partnership = $this->getPartnership(TRUE)){
      return $partnership ? $partnership->getAuthority($single) : NULL;
    }

    return NULL;
  }

  /**
   * Get the primary authority contact for this notice.
   *
   * If there is a partnership this will be the primary contact for the partnership.
   * Otherwise it will be the primary contact for the authority as a whole.
   *
   * @return ParDataEntityInterface|bool
   *   Return false if none found.
   *
   */
  public function getPrimaryAuthorityContact() {
    if ($partnership = $this->getPartnership(TRUE)) {
      $pa_contact = $partnership->getAuthorityPeople(TRUE);
    }
    elseif ($authority = $this->getPrimaryAuthority(TRUE)) {
      $pa_contact = $authority->getPerson(TRUE);
    }

    return isset($pa_contact) ? $pa_contact : NULL;
  }

  /**
   * Get the enforcing authority for this Deviation Request.
   */
  public function getEnforcingAuthority($single = FALSE) {
    $authorities = $this->get('field_enforcing_authority')->referencedEntities();
    $authority = !empty($authorities) ? current($authorities) : NULL;

    return $single ? $authority : $authorities;
  }

  /**
   * Get the Partnership for this Deviation Request.
   *
   * @param boolean $single
   *
   * @return ParDataEntityInterface|bool
   *   Return false if none found.
   *
   */
  public function getPartnership($single = FALSE) {
    $partnerships = $this->get('field_partnership')->referencedEntities();
    $partnership = !empty($partnerships) ? current($partnerships) : NULL;

    return $single ? $partnership : $partnerships;
  }

  /**
   * Get the enforcing officer person for the current Deviation Request.
   */
  public function getEnforcingPerson($single = FALSE) {
    $people = $this->get('field_person')->referencedEntities();
    $person = !empty($people) ? current($people): NULL;

    return $single ? $person : $people;
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
      ->setDescription(t('The date this inspection feedback was issued.'))
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
      ->setDescription(t('Notes about this inspection feedback.'))
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
        'file_directory' => 'documents/inspection_feedback',
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
      ->setDescription(t('Notes about this inspection feedback from the primary authority.'))
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
      ->setDescription(t('Messages relating to this inspection feedback.'))
      ->setSettings(
        [
          'default_mode' => 1,
          'per_page' => 50,
          'anonymous' => 0,
          'form_location' => 1,
          'preview' => 1,
          'comment_type' => 'par_inspection_feedback_comments',
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
          'comment_type' => 'par_inspection_feedback_comments',
          'locked' => false,

        ],
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
