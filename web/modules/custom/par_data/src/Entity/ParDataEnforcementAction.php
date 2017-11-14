<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_enforcement_action entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_enforcement_action",
 *   label = @Translation("PAR Enforcement Action"),
 *   label_collection = @Translation("PAR Enforcement Actions"),
 *   label_singular = @Translation("PAR Enforcement Action"),
 *   label_plural = @Translation("PAR Enforcement Actions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enforcement action",
 *     plural = "@count enforcement actions"
 *   ),
 *   bundle_label = @Translation("PAR Enforcement Action type"),
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
 *   base_table = "par_enforcement_actions",
 *   data_table = "par_enforcement_actions_field_data",
 *   revision_table = "par_enforcement_actions_revision",
 *   revision_data_table = "par_enforcement_actions_field_revision",
 *   admin_permission = "administer par_data_enforcement_action entities",
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
 *     "collection" = "/admin/content/par_data/par_data_enforcement_action",
 *     "canonical" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}",
 *     "edit-form" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_enforcement_action/{par_data_enforcement_action}/delete"
 *   },
 *   bundle_entity_type = "par_data_enforcement_action_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_enforcement_action_type.edit_form"
 * )
 */
class ParDataEnforcementAction extends ParDataEntity {

  const APPROVED = 'approved';
  const BLOCKED = 'blocked';
  const REFERRED = 'referred';
  const AWAITING = 'awaiting_approval';

  /**
   * Get the blocked advice for this Enforcement Action.
   */
  public function getBlockedAdvice() {
    return $this->get('field_blocked_advices')->referencedEntities();
  }

  /**
   * Get the action referrals for this Enforcement Action.
   */
  public function getActionReferral() {
    return $this->get('field_action_referral')->referencedEntities();
  }

  /**
   * Get the regulatory function for this Enforcement Action.
   */
  public function getRegulatoryFunction() {
    return $this->get('field_regulatory_function')->referencedEntities();
  }

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
   * Check if this entity is referred.
   *
   * @return bool
   */
  public function isReferred() {
    $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $current_status = $status_field ? $this->get($status_field)->getString() : NULL;
    if ($current_status === self::REFERRED) {
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
    return ($current_status === self::AWAITING);
  }

  /**
   * Approve an enforcement action.
   */
  public function approve() {
    if (!$this->isNew() && !$this->isApproved()) {
      $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
      $this->set($status_field, self::APPROVED);
      return ($this->save() === SAVED_UPDATED);
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
  public function block($authority_notes) {
    if (!$this->isNew() && !$this->isBlocked()) {
      $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
      $this->set($status_field, self::BLOCKED);
      $this->set('primary_authority_notes', $authority_notes);
      return ($this->save() === SAVED_UPDATED);
    }
    return FALSE;
  }

  /**
   *  Refer an Action of an Enforcement notification.
   *
   * @param string $authority_notes
   *  referral notes indicating the reason for the referral status update in the form.
   *
   * @return boolean
   *   True if the entity has been set to a referred state, false for all other results.
   *
   */
  public function refer($refer_notes) {
    if (!$this->isNew() && !$this->isReferred()) {
      $status_field = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
      $this->set($status_field, self::REFERRED);
      $this->set('referral_notes', $refer_notes);
      return ($this->save() === SAVED_UPDATED);
    }
    return FALSE;
  }

  /**
 *  Get the referred note data from the current action.
 *
 * @return String referred_text | NULL
 *   The referred text stored on the current action or null.
 *
 */
  public function getReferralNotes() {
    return $this->get('referral_notes')->getString();
  }

  /**
   *  Get the primary authority notes data from the current action.
   *
   * @return String primary_authority_notes | NULL
   *   The referred text stored on the current action or null.
   *
   */
  public function getPrimaryAuthorityNotes() {
    return $this->get('primary_authority_notes')->getString();
  }

  /**
   * Clone an enforcement notice action entity to support the referral process.
   *
   * @return ParDataEnforcementAction $action
   *  cloned action entity if a referral exists or NULL.
   */
  public function cloneReferredEnforcementAction($referral_authority_id, $action_id) {

    if ($referral_authority_id) {
      // Duplicate this action before changing the status.
      $cloned_action = $this->createDuplicate();
      // As we are referring an action by cloning the original we need to set the original
      // action id on the copied (referred action) this indicates that the action in question
      // has been referred once and can no longer be referred.
      $cloned_action->set('field_action_referral', $action_id);
      // If a new duplicate enforcement action has been saved to the system return it.
      if ($cloned_action->save()) {
        return $cloned_action;
      }
      else {
        throw new ParDataException("The referral action for the %action (action) cannot be created, please contact the helpdesk.");
        return FALSE;
      }
    }
    else {
    throw new ParDataException("The referral Action for %action (action) could not be created referral id missing, please contact the helpdesk.");
    return FALSE;
  }
  }

  /**
   * Clone an enforcement notice entity to support the referral process.
   *
   * @return ParDataEnforcementAction $action
   *  cloned action entity if a referral exists or NULL.
   */
  public function cloneEnforcementNotice($referral_authority_id, ParDataEnforcementAction $cloned_action, ParDataEnforcementNotice $par_data_enforcement_notice) {

    if ($referral_authority_id) {
      // Duplicate this enforcement notification and assign it the cloned action.
      $referral_notice = $par_data_enforcement_notice->createDuplicate();
      $referral_notice->set('field_primary_authority', $referral_authority_id);
      $referral_notice->set('field_enforcement_action', $cloned_action->id());

      if ($referral_notice->save()) {
        return $referral_notice;
      }
      else {
        throw new ParDataException("The referral Enforcement notice for the %action (action) cannot be created, please contact the helpdesk.");
        return FALSE;
      }

    }
    else {
      throw new ParDataException("The referral Enforcement Notice for the %action action could not be created referral id missing, please contact the helpdesk.");
      return FALSE;
    }
  }

  /**
   * Archive if the entity is archivable and is not new.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function archive() {
    if (!$this->isNew() && $this->getTypeEntity()->isArchivable() && !$this->isArchived()) {
      $this->set(ParDataEntity::ARCHIVE_FIELD, TRUE);
      return ($this->save() === SAVED_UPDATED);
    }
    return FALSE;
  }

  /**
   * Restore an archived entity.
   *
   * @return boolean
   *   True if the entity was restored, false for all other results.
   */
  public function restore() {
    if (!$this->isNew() && $this->getTypeEntity()->isRevokable() && $this->isArchived()) {
      $this->set(ParDataEntity::ARCHIVE_FIELD, FALSE);
      return ($this->save() === SAVED_UPDATED);
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Summary.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Title of the enforcement action.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 1000,
        'text_processing' => 0,
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

    // Details.
    $fields['details'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Summary'))
      ->setDescription(t('Details about this enforcement action.'))
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

    // Enforcement status.
    $fields['enforcement_action_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Enforcement Action Status'))
      ->setDescription(t('The status of the current enforcement action.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Enforcement notes.
    $fields['enforcement_action_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Enforcement Action Notes'))
      ->setDescription(t('Notes about this enforcement action.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 4,
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

    // Primary Authority status.
    $fields['primary_authority_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Primary Authority Status'))
      ->setDescription(t('The status of the primary authority on this action.'))
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
      ->setDescription(t('Notes about this enforcement action from the primary authority.'))
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

    // Referral notes.
    $fields['referral_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Referral Notes'))
      ->setDescription(t('Referral notes.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 7,
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
      ->setDescription(t('Documents relating to the enforcement action.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSettings([
        'target_type' => 'file',
        'uri_scheme' => 's3private',
        'max_filesize' => '20 MB',
        'file_extensions' => 'jpg jpeg gif png tif pdf txt rdf doc docx odt xls xlsx csv ods ppt pptx odp pot potx pps',
        'file_directory' => 'documents/enforcement',
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

    return $fields;
  }

}
