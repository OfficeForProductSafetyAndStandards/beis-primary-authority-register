<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\link\LinkItemInterface;
use Drupal\user\UserInterface;

/**
 * Defines the par_data_partnership entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_partnership",
 *   label = @Translation("PAR Partnership"),
 *   label_collection = @Translation("PAR Partnerships"),
 *   label_singular = @Translation("PAR Partnership"),
 *   label_plural = @Translation("PAR Partnerships"),
 *   label_count = @PluralTranslation(
 *     singular = "@count partnership",
 *     plural = "@count partnerships"
 *   ),
 *   bundle_label = @Translation("PAR Partnership type"),
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
 *   base_table = "par_partnerships",
 *   data_table = "par_partnerships_field_data",
 *   revision_table = "par_partnerships_revision",
 *   revision_data_table = "par_partnerships_field_revision",
 *   admin_permission = "administer par_data_partnership entities",
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
 *     "collection" = "/admin/content/par_data/par_data_partnership",
 *     "canonical" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}",
 *     "edit-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_partnership/{par_data_partnership}/delete"
 *   },
 *   bundle_entity_type = "par_data_partnership_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_partnership_type.edit_form"
 * )
 */
class ParDataPartnership extends ParDataEntity {

  /**
   * The length of time to obtain a lock for.
   */
  const LOCK_TIMEOUT = 3600.0;

  const ADVICE_REVOKE_REASON = 'Partnership entity revoked';
  const INSPECTION_PLAN_REVOKE_REASON = 'Partnership entity revoked';

  /**
   * The available member display options.
   */
  const MEMBER_DISPLAY_INTERNAL = 'internal';
  const MEMBER_DISPLAY_EXTERNAL = 'external';
  const MEMBER_DISPLAY_REQUEST = 'request';

  /**
   * The revision prefix for identifying when the organisation last updated the list.
   */
  const MEMBER_LIST_REVISION_PREFIX = 'PAR_MEMBER_LIST_UPDATE';

  /**
   * Get the time service.
   */
  public function getTime() {
    return \Drupal::time();
  }

  /**
   * Get the lock service.
   */
  public function getLock() {
    return \Drupal::service('lock.persistent');
  }

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // Exclude any references to partnerships, this is a one-way relationship.
        // Partnerships relate to enforcement notices but not the other way round.
        if ($relationship->getEntity()->getEntityTypeId() === 'par_data_enforcement_notice'
          && $enforcement_primary_authority = $relationship->getEntity()->getPrimaryAuthority(TRUE)) {
          $partnership_primary_authority = $relationship->getBaseEntity()->getAuthority(TRUE);
          return ($enforcement_primary_authority->uuid() === $partnership_primary_authority->uuid());
        }

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($save = TRUE, $reason = '') {
    // Revoke/archive all dependent entities as well.
    $inspection_plans = $this->getInspectionPlan();
    foreach ($inspection_plans as $inspection_plan) {
      // Set default revoke reason when the partnership has initiated the revoke.
      $inspection_plan->revoke($save, $this::INSPECTION_PLAN_REVOKE_REASON);
    }

    $advice_documents = $this->getAdvice();
    foreach ($advice_documents as $advice) {
      // Set default revoke reason when the partnership has initiated the revoke.
      $advice->revoke($save, $this::ADVICE_REVOKE_REASON);
    }

    return parent::revoke($save, $reason);
  }

  /**
   * {@inheritdoc}
   */
  public function unrevoke($save = TRUE) {
    // Revoke/archive all dependent entities as well.
    $inspection_plans = $this->getInspectionPlan();
    foreach ($inspection_plans as $inspection_plan) {
      $inspection_plan->unrevoke($save);
    }

    $advice_documents = $this->getAdvice();
    foreach ($advice_documents as $advice) {
      $advice->unrevoke($save);
    }

    parent::unrevoke($save);
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    // Whether an entity is complete and can be acted upon as a finished, live.
    $awaiting_statuses = [
      $this->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'confirmed_business'
    ];

    if (in_array($this->getRawStatus(), $awaiting_statuses)) {
      return FALSE;
    }

    return parent::isActive();
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Freeze partnerships that are awaiting approval.
    $awaiting_statuses = [
      $this->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'confirmed_business'
    ];

    if (in_array($this->getRawStatus(), $awaiting_statuses)) {
      return TRUE;
    }

    // Freeze partnerships that have un approved enforcement notices
    $enforcement_notices = $this->getRelationships('par_data_enforcement_notice');
    foreach ($enforcement_notices as $uuid => $relationship) {
      if ($relationship->getEntity()->inProgress()) {
        return TRUE;
      }
    }

    return parent::inProgress();
  }

  /**
   * Generate filename.
   */
  public function membershipLockKey() {
    // The lock key cannot be longer than this.
    return "par_member_list__{$this->uuid()}";
  }

  /**
   * Lock the membership list.
   *
   * Don't allow this lock to be reacquired by the same process.
   */
  public function lockMembership() {
    return $this->isMembershipLocked() ? FALSE : $this->getLock()->acquire($this->membershipLockKey(), self::LOCK_TIMEOUT);
  }

  /**
   * Unlock the membership list.
   */
  public function unlockMembership() {
    $this->getLock()->release($this->membershipLockKey());
  }

  /**
   * Check if the partnership is locked.
   */
  public function isMembershipLocked() {
    return !$this->getLock()->lockMayBeAvailable($this->membershipLockKey());
  }

  /**
   * Get the number of coordinated members added to this partnership's member list.
   *
   * @param int $i
   *   The index to start counting from, can be used to add up all members.
   * @param bool $include_expired
   *   Whether to include expired members. By default only active members are returned.
   *
   * @return int
   *   The number of active members.
   */
  public function countMembers($i = 0, $include_expired = FALSE) {
    foreach ($this->getCoordinatedMember() as $member) {
      if ($include_expired || !$member->isRevoked()) {
        $i++;
      }
    }
    return $i;
  }

  /**
   * Get the number of members associated with this partnership.
   *
   * Note this method reports the number of members a coordinator says they have
   * as opposed to self::countMembers() which retrieves the number of coordinated
   * members attached to the partnerhip's member list (only used for 'internal' lists).
   *
   * @return int
   *  The number of active members.
   */
  public function numberOfMembers() {
    // PAR-1741: Use the display method to determine how to get the number of members.
    switch ($this->getMemberDisplay()) {
      case self::MEMBER_DISPLAY_INTERNAL:
        return $this->countMembers();

        break;
      case self::MEMBER_DISPLAY_EXTERNAL:
      case self::MEMBER_DISPLAY_REQUEST:
        return !$this->get('member_number')->isEmpty() ?
          (int) $this->get('member_number')->getString() : 0;

        break;
    }

    return 0;
  }

  /**
   * Get the time the membership list was last updated.
   *
   * @return bool
   *  Whether the member list needs updating.
   *  TRUE if it hasn't been updated recently
   *  FALSE if it has been updated recently
   */
  public function memberListNeedsUpdating($since = '-3 months') {
    // Make sure not to request this more than once for a given entity.
    $function_id = __FUNCTION__ . ':' . $this->uuid();
    $status_revision = &drupal_static($function_id);
    if (!empty($status_revision)) {
      return $status_revision;
    }

    // Only for coordinated partnerships.
    if (!$this->isCoordinated()) {
      return FALSE;
    }

    $partnership_storage = $this->entityTypeManager()->getStorage($this->getEntityTypeId());

    // Query any member list update revisions since the last cutoff time.
    $timestamp = strtotime($since);
    $revision_query = $partnership_storage->getQuery()->allRevisions()
      ->condition('id', $this->id())
      ->condition($this->getEntityType()->getRevisionMetadataKey('revision_log_message'), self::MEMBER_LIST_REVISION_PREFIX, 'STARTS_WITH')
      ->condition($this->getEntityType()->getRevisionMetadataKey('revision_created'), $timestamp, '>=')
      ->sort($this->getEntityType()->getRevisionMetadataKey('revision_created'), 'DESC');

    $count = $revision_query->count()->execute();
    if ($count > 0) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Get the membership link.
   *
   * @return \Drupal\Core\Url
   *  The URL for the external member link.
   */
  public function getMemberLink() {
    $url = !$this->get('member_link')->isEmpty()
        ? $this->get('member_link')->first()->getUrl()
        : NULL;

    return $url instanceof Url ? $url : NULL;
  }

  /**
   * Get the membership list display type.
   *
   * @return string
   *  The user configured method of displaying the member list.
   */
  public function getMemberDisplay() {
    if (!$this->get('member_display')->isEmpty()) {
      $display = $this->get('member_display')->getString();
      // The display value must be one of self::MEMBER_DISPLAY_INTERNAL,
      // self::MEMBER_DISPLAY_EXTERNAL or self::MEMBER_DISPLAY_REQUEST
      // as defined in the entity type config.
      $allowed_displays = $this->getTypeEntity()->getAllowedValues('member_display');
      if (isset($allowed_displays[$display])) {
        return $display;
      }
    }

    // The default value is determined by whether any coordinated members have
    // ever been uploaded or else whether the member_number field is empty.
    return $this->getDefaultMemberDisplay();
  }

  /**
   * @return string
   *  The default
   */
  public function getDefaultMemberDisplay() {
    // If there are any uploaded members default to an internal list.
    if ($this->countMembers(0, true) > 0) {
      return self::MEMBER_DISPLAY_INTERNAL;
    }

    // If the member number field has been filled.
    if (!$this->get('member_link')->isEmpty()) {
      return self::MEMBER_DISPLAY_EXTERNAL;
    }

    // If the member number field has been filled.
    if (!$this->get('member_number')->isEmpty()) {
      return self::MEMBER_DISPLAY_REQUEST;
    }

    // Internal lists are the preferred default if no action has been taken.
    return self::MEMBER_DISPLAY_INTERNAL;
  }

  /**
   * Get the organisation contacts for this Partnership.
   */
  public function getOrganisationPeople($primary = FALSE) {
    /** @var \Drupal\par_data\Entity\ParDataPersonInterface[] $people */
    $people = $this->get('field_organisation_person')->referencedEntities();

    // PAR-1690: Filter out any contact records that are empty.
    $people = array_filter($people, function ($person) {
      return ($person instanceof ParDataEntityInterface && !empty($person->getEmail()));
    });

    $person = !empty($people) ? current($people) : NULL;

    return $primary ? $person : $people;
  }

  /**
   * Get the authority contacts for this Partnership.
   */
  public function getAuthorityPeople($primary = FALSE) {
    /** @var \Drupal\par_data\Entity\ParDataPersonInterface[] $people */
    $people = $this->get('field_authority_person')->referencedEntities();

    // PAR-1690: Filter out any contact records that are empty.
    $people = array_filter($people, function ($person) {
      return ($person instanceof ParDataPersonInterface && !empty($person->getEmail()));
    });

    $person = !empty($people) ? current($people) : NULL;

    return $primary ? $person : $people;
  }

  /**
   * Get the organisation for this Partnership.
   */
  public function getOrganisation($single = FALSE) {
    $organisations = $this->get('field_organisation')->referencedEntities();
    $organisation = !empty($organisations) ? current($organisations) : NULL;

    return $single ? $organisation : $organisations;
  }

  /**
   * Get the authority for this Partnership.
   */
  public function getAuthority($single = FALSE) {
    $authorities = $this->get('field_authority')->referencedEntities();
    $authority = !empty($authorities) ? current($authorities) : NULL;

    return $single ? $authority : $authorities;
  }

  /**
   * Get the coordinated members for this Partnership.
   */
  public function getCoordinatedMember($single = FALSE, $active = FALSE) {
    $members = $this->get('field_coordinated_business')->referencedEntities();

    // Ignore deleted members.
    $members = array_filter($members, function ($member) {
      return !$member->isDeleted();
    });

    if ($active) {
      // Ignore ceased members if only active members have been requested.
      $members = array_filter($members, function ($member) {
        return !$member->isCeased();
      });
    }
    $member = !empty($members) ? current($members) : NULL;

    return $single ? $member : $members;
  }

  /**
   * Get the advice entities for this Partnership.
   */
  public function getAdvice($single = FALSE) {
    $documents = $this->get('field_advice')->referencedEntities();
    $document = !empty($documents) ? current($documents) : NULL;

    return $single ? $document : $documents;
  }

  /**
   * Get the inspection plans for this Partnership.
   */
  public function getInspectionPlan($single = FALSE) {
    $plans = $this->get('field_inspection_plan')->referencedEntities();
    $plan = !empty($plans) ? current($plans) : NULL;

    return $single ? $plan : $plans;
  }

  /**
   * Get the regulatory functions for this Partnership.
   */
  public function getRegulatoryFunction($single = FALSE) {
    $regulatory_functions = $this->get('field_regulatory_function')->referencedEntities();
    $regulatory_function = !empty($regulatory_functions) ? current($regulatory_functions) : NULL;

    return $single ? $regulatory_function : $regulatory_functions;
  }

  /**
   * Check if a par person is a member of the organisation.
   *
   * {@deprecated}
   *
   * @param ParDataPerson $person
   *   A PAR Person to check for.
   *
   * @return boolean
   *   Whether the person is an organisation member or not.
   */
  public function personIisOrganisationMember(ParDataPerson $person) {
    $authority_people_ids = $this->retrieveEntityIds('field_authority_person');
    return in_array($person->id(), $authority_people_ids);
  }

  /**
   * Check if a par person is a member of the Authority.
   *
   * {@deprecated}
   *
   * @param ParDataPerson $person
   *   A PAR Person to check for.
   *
   * @return boolean
   *   Whether the person is an authority member or not.
   */
  public function personIsAuthorityMember(ParDataPerson $person) {
    $authority_people_ids = $this->retrieveEntityIds('field_organisation_person');
    return in_array($person->id(), $authority_people_ids);
  }

  /**
   * Check if a user is a member of the Authority.
   *
   * @param AccountInterface $account
   *   A Drupal user account to check for.
   *
   * @return boolean
   *   Whether the user is an authority member or not.
   */
  public function isOrganisationMember(AccountInterface $account) {
    $organisation_people_ids = $this->retrieveEntityIds('field_organisation_person');
    $current_user_people = $this->getParDataManager()->getUserPeople($account);

    if (!empty($organisation_people_ids) && !empty($current_user_people)) {
      return array_intersect_key(array_flip($organisation_people_ids), $current_user_people);
    }
    else {
      return FALSE;
    }
  }

  /**
   * Check if a user is a member of the Authority.
   *
   * @param AccountInterface $account
   *   A Drupal user account to check for.
   *
   * @return boolean
   *   Whether the user is an authority member or not.
   */
  public function isAuthorityMember(AccountInterface $account) {
    $authority_people_ids = $this->retrieveEntityIds('field_authority_person');
    $current_user_people = $this->getParDataManager()->getUserPeople($account);

    if (!empty($authority_people_ids) && !empty($current_user_people)) {
      return array_intersect_key(array_flip($authority_people_ids), $current_user_people);
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get all the names of regulatory functions associated with the partnership.
   *
   * @return array()
   *   An array containing all the regulatory function names associated with the current partnership.
   */
  public function getPartnershipRegulatoryFunctionNames() {
    $partnership_regulatory_functions = $this->getRegulatoryFunction();

    $partnership_reg_fun_name_list = array();

    foreach ($partnership_regulatory_functions as $key => $regulatory_function_entity) {
      $partnership_reg_fun_name_list[$regulatory_function_entity->get('id')->getString()] =  $regulatory_function_entity->get('function_name')->getString();
    }

    return $partnership_reg_fun_name_list;
  }

  public function isDirect() {
    return $this->get('partnership_type')->getString() === 'direct';
  }

  public function isCoordinated() {
    return $this->get('partnership_type')->getString() === 'coordinated';
  }

  /**
   * Get legal entities for this partnership.
   */
  public function getLegalEntity($single = FALSE) {
    $legal_entities = $this->get('field_legal_entity')->referencedEntities();
    $legal_entity = !empty($legal_entities) ? current($legal_entities) : NULL;

    return $single ? $legal_entity : $legal_entities;
  }

  /**
   * Add a legal entity to partnership.
   *
   * @param ParDataLegalEntity $legal_entity
   *   A PAR Legal Entity to add.
   */
  public function addLegalEntity(ParDataLegalEntity $legal_entity) {
    // Retrieve existing legal entities.
    $legal_entities = $this->getLegalEntity();

    // Append new legal entity to existing entities.
    $legal_entities[] = $legal_entity;

    $this->set('field_legal_entity', $legal_entities);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Partnership Type.
    $fields['partnership_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Partnership Type'))
      ->setDescription(t('The type of partnership.'))
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

    // Partnership Status.
    $fields['partnership_status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Partnership Status'))
      ->setDescription(t('The current status of the partnership plan itself. For example, current, expired, replaced.'))
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

    // About Partnership.
    $fields['about_partnership'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('About the Partnership'))
      ->setDescription(t('Details about this partnership.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 25,
        'settings' => [
          'rows' => 3,
        ],
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Approved Date.
    $fields['approved_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Approved Date'))
      ->setDescription(t('The date this partnership was approved.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership Status.
    $fields['member_display'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Member list display'))
      ->setDescription(t('The list display type, one of: internal, external, request.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Number of Members.
    $fields['member_number'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Number of members'))
      ->setDescription(t('The number of coordinated members in this partnership.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 6,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Number of Members.
    $fields['member_link'] = BaseFieldDefinition::create('link')
      ->setLabel(t('Member list link'))
      ->setDescription(t('The link to the publicly available external member list.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'link_type' => LinkItemInterface::LINK_EXTERNAL,
        'title' => DRUPAL_DISABLED,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'link_default',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'link',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);


    // Partnership Status.
    $fields['cost_recovery'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Cost Recovery'))
      ->setDescription(t('How is the cost recovered by for this partnership.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 9,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Rejected Comment.
    $fields['reject_comment'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Reject Comment'))
      ->setDescription(t('Comments about why this partnership was rejected.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
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

    // Recovation Source.
    $fields['revocation_source'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Revocation Source'))
      ->setDescription(t('Who was responsible for revoking this partnership.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 11,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Recovation Date.
    $fields['revocation_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Recovation Date'))
      ->setDescription(t('The date this partnership was revoked.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'datetime_type' => 'date',
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Authority Change Comment.
    $fields['authority_change_comment'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Authority Change Comment'))
      ->setDescription(t('Comments by the authority when this partnership was changed.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 14,
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

    // Organisation Change Comment.
    $fields['organisation_change_comment'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Organisation Change Comment'))
      ->setDescription(t('Comments by the organisation when this partnership was changed.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 15,
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

    // Terms and conditions agreed by organisation.
    $fields['terms_organisation_agreed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Organisation Terms and Conditions'))
      ->setDescription(t('Terms and conditions agreed by organisation.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 22,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Terms and conditions agreed by authority.
    $fields['terms_authority_agreed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Authority Terms and Conditions'))
      ->setDescription(t('Terms and conditions agreed by authority.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 23,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Coordinator suitable.
    $fields['coordinator_suitable'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Coordinator Suitable'))
      ->setDescription(t('Is coordinator suitable.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 24,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership info confirmed by authority.
    $fields['partnership_info_agreed_authority'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Authority Information Agreed'))
      ->setDescription(t('The partnership information has been agreed by the authority.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 25,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Partnership info confirmed by business.
    $fields['partnership_info_agreed_business'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Business Information Agreed'))
      ->setDescription(t('The partnership information has been agreed by the business.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 26,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Written summary agreed.
    $fields['written_summary_agreed'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Written Summary Agreed'))
      ->setDescription(t('A written summary has been agreed between the authority and the organisation.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 27,
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
