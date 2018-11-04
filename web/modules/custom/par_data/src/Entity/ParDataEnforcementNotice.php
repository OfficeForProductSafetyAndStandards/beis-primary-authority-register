<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_data\ParDataException;

/**
 * Defines the par_data_enforcement_notice entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_enforcement_notice",
 *   label = @Translation("PAR Enforcement Notice"),
 *   label_collection = @Translation("PAR Enforcement Notices"),
 *   label_singular = @Translation("PAR Enforcement Notice"),
 *   label_plural = @Translation("PAR Enforcement Notices"),
 *   label_count = @PluralTranslation(
 *     singular = "@count enforcement notice",
 *     plural = "@count enforcement notices"
 *   ),
 *   bundle_label = @Translation("PAR Enforcement Notice type"),
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
 *   base_table = "par_enforcement_notices",
 *   data_table = "par_enforcement_notices_field_data",
 *   revision_table = "par_enforcement_notices_revision",
 *   revision_data_table = "par_enforcement_notices_field_revision",
 *   admin_permission = "administer par_data_enforcement_notice entities",
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
 *     "collection" = "/admin/content/par_data/par_data_enforcement_notice",
 *     "canonical" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}",
 *     "edit-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_enforcement_notice/{par_data_enforcement_notice}/delete"
 *   },
 *   bundle_entity_type = "par_data_enforcement_notice_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_enforcement_notice_type.edit_form"
 * )
 */
class ParDataEnforcementNotice extends ParDataEntity {

  use ParEnforcementEntityTrait;

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
      // Exclude any references to partnerships, this is a one-way relationship.
      // Partnerships relate to enforcement notices but not the other way round.
      if ($relationship->getEntity()->getEntityTypeId() === 'par_data_partnership') {
        return FALSE;
      }

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Any Enforcement Notices with actions that are awaiting approval are marked as 'in progress'.
    foreach ($this->getEnforcementActions() as $action) {
      if ($action->inProgress()) {
        return TRUE;
      }
    }

    return parent::inProgress();
  }

  /**
   * If this is a referred notice get the original notice.
   *
   * @return ParDataEntityInterface|bool
   *   Return false if not referred.
   */
  public function getReferringNotice() {
    foreach ($this->getEnforcementActions() as $action) {
      if ($action->isReferred() && ($referred_from = $action->getActionReferral())) {
          return $referred_from->getEnforcementNotice();
      }
    }

    return FALSE;
  }

  /**
   * Get the legal entity for this Enforcement Notice.
   */
  public function getEnforcedEntityName() {
    if ($legal_entity = $this->getLegalEntity(TRUE)) {
      return $legal_entity->label();
    }
    elseif (!$this->get('legal_entity_name')->isEmpty()) {
      return $this->get('legal_entity_name')->getString();
    }
    elseif ($organisation = $this->getEnforcedOrganisation(TRUE)) {
      return $organisation->label();
    }

    return NULL;
  }

  /**
   * Get the legal entity for this Enforcement Notice.
   */
  public function getLegalEntity($single = FALSE) {
    $legal_entities = $this->get('field_legal_entity')->referencedEntities();
    $legal_entity = !empty($legal_entities) ? current($legal_entities) : NULL;

    return $single ? $legal_entity : $legal_entities;
  }

  /**
   * Get the enforcement actions for this Enforcement Notice.
   */
  public function getEnforcementActions() {
    return $this->get('field_enforcement_action')->referencedEntities();
  }

  /**
   * Get all authorities to which this notice can be referred.
   *
   * @param array $authorities
   *   An array of authorities that can be appended to.
   *
   * @return array
   *   An array of authorities that actions of this notice can be referred to.
   */
  public function getReferrableAuthorities($authorities = []) {
    $par_data_organisation = $this->getEnforcedOrganisation(TRUE);

    // If no organisation is found this notice cannot be referred.
    if (!$par_data_organisation) {
      return $authorities;
    }

    $primary_authority = $this->getPrimaryAuthority(TRUE);

    // Get all partnerships with the same organisation,
    // that aren't deleted and were transitioned.
    $conditions = [
      'name' => [
        'AND' => [
          ['field_organisation', $par_data_organisation->id()],
        ]
      ],
    ];

    $par_data_partnerships = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions, 10);

    // Load all the authorities belonging to these partnerships.
    foreach ($par_data_partnerships as $partnership) {
      $authority = $partnership->getAuthority(TRUE);

      if ($partnership->isLiving() && $authority->isLiving() && $authority->id() != $primary_authority->id()) {
        $authorities[$authority->id()] = $authority->label();
      }
    }

    return $authorities;
  }

  /**
   * Approve all actions of an enforcement notice.
   */
  public function approve() {
    foreach ($this->getEnforcementActions() as $action) {
      $action->approve();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRawStatus() {
    if ($this->isDeleted()) {
      return 'deleted';
    }
    if ($this->isRevoked()) {
      return 'revoked';
    }
    if ($this->isArchived()) {
      return 'archived';
    }
    if (!$this->isTransitioned()) {
      return 'n/a';
    }

    // Loop through all actions to determine status.
    foreach ($this->getEnforcementActions() as $action) {
      if ($action->isAwaitingApproval()) {
        return 'unknown';
      }
      if ($action->isReviewed()) {
        $status = 'reviewed';
      }
    }

    return isset($status) ? $status : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getParStatus() {
    if ($this->isDeleted()) {
      return 'Deleted';
    }
    if ($this->isRevoked()) {
      return 'Revoked';
    }
    if ($this->isArchived()) {
      return 'Archived';
    }
    if (!$this->isTransitioned()) {
      return 'Not transitioned from PAR2';
    }

    $field_name = $this->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
    $raw_status = $this->getRawStatus();
    return $field_name && $raw_status ? $this->getTypeEntity()->getAllowedFieldlabel($field_name, $raw_status) : '';
  }

  /**
   * Clone an enforcement notice entity to support the referral process.
   *
   * @return ParDataEnforcementNotice
   *   Cloned notice entity if a referral exists or NULL.
   */
  public function cloneNotice($referral_authority_id, ParDataEnforcementAction $cloned_action) {
    if ($referral_authority_id && $primary_authority = ParDataAuthority::load($referral_authority_id)) {
      // Duplicate this enforcement notification and assign it the cloned action.
      $referral_notice = $this->createDuplicate();

      $referral_notice->set('field_primary_authority', $primary_authority->id());
      $referral_notice->set('field_person', $primary_authority->getPerson(TRUE));
      $referral_notice->set('field_enforcement_action', $cloned_action->id());

      $date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
      $referral_notice->set('notice_date', $date->format('Y-m-d'));

      if ($referral_notice->save()) {
        return $referral_notice;
      }
      else {
        throw new ParDataException("The referral Enforcement notice for the %action (action) cannot be created, please contact the helpdesk.");
      }

    }
    else {
      throw new ParDataException("The referral Enforcement Notice for the %action action could not be created referral id missing, please contact the helpdesk.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Notice Type.
    $fields['notice_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Notice Type'))
      ->setDescription(t('The type of enforcement notice.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
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

    // Notice Date.
    $fields['notice_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Notice Date'))
      ->setDescription(t('The date this enforcement notice was issued.'))
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

    // Notice Type.
    $fields['legal_entity_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Legal Entity Name'))
      ->setDescription(t('An optional free text field for entering a legal entity name.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
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

    // Summary.
    $fields['summary'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Summary'))
      ->setDescription(t('Summary about this enforcement notice.'))
      ->setRevisionable(TRUE)
      ->addConstraint('par_required')
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

    return $fields;
  }

}
