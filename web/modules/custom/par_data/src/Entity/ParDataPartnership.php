<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\Type\DateTimeInterface;
use Drupal\Core\Url;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\link\LinkItemInterface;
use Drupal\par_data\ParDataException;
use Drupal\par_data\Plugin\Field\FieldType\ParMemberListUpdatedField;
use Drupal\par_data\Plugin\Field\FieldType\ParMembersField;

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
   * The revision prefix for identifying when the partnership name changed.
   */
  const PARTNERSHIP_NAME_CHANGE = 'PAR_PARTNERSHIP_NAME_CHANGE';

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
  public function nominate($save = TRUE) {
    // Do not nominate partnerships that are already nominated.
    if ($this->isActive()) {
      return FALSE;
    }

    // Set the status to active.
    $this->setParStatus('confirmed_rd');

    // Set the approved date.
    $current_date = new DrupalDateTime();
    $this->setApprovedDate($current_date);

    // Ensure that nominating a revoked partnership succeeds.
    $this->unrevoke(FALSE);

    // Save the changes.
    $completed = !$save || $this->save() === SAVED_UPDATED || $this->save() === SAVED_NEW;

    if ($completed) {
      // Nominate all the partnership legal entities after the partnership is saved.
      foreach ($this->getPartnershipLegalEntities() as $legal_entity) {
        try {
          $legal_entity->nominate($save);
        }
        catch (ParDataException $ignore) {

        }
      }
    }

    return $completed;
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($save = TRUE, $reason = '') {
    // Do not revoke partnerships that are already revoked.
    if ($this->isRevoked()) {
      return FALSE;
    }

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

    // Set the revocation date.
    $current_date = new DrupalDateTime();
    $this->setRevocationDate($current_date);

    // Save the changes.
    return parent::revoke($save, $reason);
  }

  /**
   * {@inheritdoc}
   */
  public function unrevoke($save = TRUE) {
    // Only restore partnerships that are revoked.
    if (!$this->isRevoked()) {
      return FALSE;
    }

    // Revoke/archive all dependent entities as well.
    $inspection_plans = $this->getInspectionPlan();
    foreach ($inspection_plans as $inspection_plan) {
      $inspection_plan->unrevoke($save);
    }

    $advice_documents = $this->getAdvice();
    foreach ($advice_documents as $advice) {
      $advice->unrevoke($save);
    }

    // Set the revocation date.
    $this->setRevocationDate(NULL);

    return parent::unrevoke($save);
  }

  /**
   * {@inheritdoc}
   */
  public function isDeletable() {
    // Get the current date.
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);

    // Rule 1: Check if the partnership isn't active or was nominated in the last day.
    $inactive_or_recently_approved = $this->isPending() ||
      $this->getApprovedDate() > $now->modify('-1 day');

    // Rule 2: Check there are no pending enforcement notices on this partnership.
    $enforcement_notices = $this->getRelationships('par_data_enforcement_notice');
    $enforcement_notices = array_filter($enforcement_notices, function ($relationship) {
      return $relationship->getEntity()->inProgress();
    });
    $no_pending_enforcements = empty($enforcement_notices);

    // Only some PAR entities can be deleted.
    return parent::isDeletable() &&
      $inactive_or_recently_approved &&
      $no_pending_enforcements;
  }

  /**
   * {@inheritdoc}
   */
  public function isRevocable() {
    // Rule 1: Check if the partnership is active and was nominated more than 1 day ago.
    $is_active = $this->isActive();

    // Rule 2: Check there are no pending enforcement notices on this partnership.
    $enforcement_notices = $this->getRelationships('par_data_enforcement_notice');
    $enforcement_notices = array_filter($enforcement_notices, function ($relationship) {
      return $relationship->getEntity()->inProgress();
    });
    $no_pending_enforcements = empty($enforcement_notices);

    // Only some PAR entities can be deleted.
    return parent::isRevocable() &&
      $is_active &&
      $no_pending_enforcements;
  }

  /**
   * {@inheritdoc}
   */
  public function isRestorable() {
    // Get the current date.
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);

    // Rule 1: Check if the partnership was revoked in the last day.
    $recently_revoked = $this->getRevocationDate() &&
      $this->getRevocationDate() > $now->modify('-1 day');

    // Only some PAR entities can be deleted.
    return parent::isRestorable() &&
      $recently_revoked;
  }

  /**
   * {@inheritdoc}
   */
  public function isActive() {
    return parent::isActive() &&
      !$this->isPending();
  }

  /**
   * {@inheritdoc}
   */
  public function isPending() {
    $awaiting_statuses = [
      $this->getTypeEntity()->getDefaultStatus(),
      'confirmed_authority',
      'confirmed_business'
    ];

    return in_array($this->getRawStatus(), $awaiting_statuses);
  }

  /**
   * {@inheritdoc}
   */
  public function inProgress() {
    // Freeze partnerships that have un approved enforcement notices
    $enforcement_notices = $this->getRelationships('par_data_enforcement_notice');
    foreach ($enforcement_notices as $uuid => $relationship) {
      if ($relationship->getEntity()->inProgress()) {
        return TRUE;
      }
    }

    // Freeze partnerships that are awaiting approval.
    return $this->isPending();
  }

  /**
   * Override the default status time.
   *
   * {@inheritdoc}
   */
  public function getStatusTime($status) {
    switch ($status) {
      case 'confirmed_rd':
        $status_time = $this->getApprovedDateField()?->getTimestamp();
        return $status_time ?? parent::getStatusTime($status);

      case self::REVOKE_FIELD:
        $status_time = $this->getRevocationDateField()?->getTimestamp();
        return $status_time ?? parent::getStatusTime($status);

      default:
        return parent::getStatusTime($status);
    }
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
    // Make sure not to request this more than once for a given entity.
    $function_id = __FUNCTION__ . ':' . $this->uuid();
    $members = &drupal_static($function_id);
    if (isset($members)) {
      return $members;
    }

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
   * @return int|null The timestamp for the last updated time.
   */
  public function membersLastUpdated(): ?int {
    // Make sure not to request this more than once for a given entity.
    $function_id = __FUNCTION__ . ':' . $this->uuid();
    $timestamp = &drupal_static($function_id);
    if (isset($timestamp)) {
      return $timestamp;
    }

    // PAR-1750: Use the display method to determine how to determine the last updated date.
    switch ($this->getMemberDisplay()) {
      case self::MEMBER_DISPLAY_INTERNAL:
        $coordinated_businesses = $this->retrieveEntityIds('field_coordinated_business');

        if ($coordinated_businesses) {
          $member_storage = $this->entityTypeManager()->getStorage('par_data_coordinated_business');
          $member_query = $member_storage->getQuery()->accessCheck()
            ->condition('id', $coordinated_businesses, 'IN')
            ->sort('changed', 'DESC')
            ->range(0, 1);

          $results = $member_query->execute();
          foreach ($results as $revision_key => $entity_id) {
            $entity = $member_storage->load($entity_id);
            return $entity ?->get('changed')->getString();
          }
        }

        break;
      case self::MEMBER_DISPLAY_EXTERNAL:
      case self::MEMBER_DISPLAY_REQUEST:
        $partnership_storage = $this->entityTypeManager()->getStorage($this->getEntityTypeId());

        $revision_query = $partnership_storage->getQuery()->accessCheck()->allRevisions()
          ->condition('id', $this->id())
          ->condition($this->getEntityType()->getRevisionMetadataKey('revision_log_message'), self::MEMBER_LIST_REVISION_PREFIX, 'STARTS_WITH')
          ->sort($this->getEntityType()->getRevisionMetadataKey('revision_created'), 'DESC')
          ->range(0, 1);

        $results = $revision_query->execute();
        foreach ($results as $revision_key => $entity_id) {
          $revision = $partnership_storage->loadRevision($revision_key);
          return $revision ?->get($this->getEntityType()->getRevisionMetadataKey('revision_created'))->getString();
        }

        break;
    }

    return NULL;
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
    // Only for coordinated partnerships.
    if (!$this->isCoordinated()) {
      return FALSE;
    }

    // If there is no last updated timestamp return needs updating.
    $last_updated = $this->membersLastUpdated();
    if (!$last_updated) {
      return TRUE;
    }

    // Get comparable timestamp.
    try {
      $since_datetime = new DrupalDateTime($since);
    } catch (Exception $e) {
      throw new ParDataException('Date format incorrect when comparing membership last updated date.');
    }

    // Compare timestamps.
    return $last_updated <= $since_datetime->getTimestamp();
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
   * Get the approved date for this partnership.
   *
   * Note: The legacy method for looking up approval dates uses the entity
   * revisions logs, if no approved date is found lookup the revision.
   */
  public function getApprovedDate(): ?DrupalDateTime {
    // Get the approval date.
    if ($date = $this->getApprovedDateField()) {
      return $date;
    }
    // Or look it up using the entity's revisions logs.
    else if ($timestamp = $this->getStatusTime('confirmed_rd')) {
      return DrupalDateTime::createFromTimestamp($timestamp);
    }

    return NULL;
  }

  private function getApprovedDateField(): ?DrupalDateTime {
    return $this->hasField('approved_date') && !$this->get('approved_date')->isEmpty() ?
      $this->approved_date?->date :
      NULL;
  }

  /**
   * Sets the date the partnership was nominated.
   *
   * @param ?DrupalDateTime $date
   *   The date this partnership was nominated.
   */
  public function setApprovedDate(DrupalDateTime $date = NULL): void {
    $this->set('approved_date', $date?->format('Y-m-d'));
  }

  /**
   * Get the revocation date for this partnership.
   *
   * Note: The legacy method for looking up revocation dates uses the entity
   * revisions logs, if no revocation date is found lookup the revision.
   */
  public function getRevocationDate(): ?DrupalDateTime {
    // Get the revocation date.
    if ($date = $this->getRevocationDateField()) {
      return $date;
    }
    // Or look it up using the entity's revisions logs.
    else if ($timestamp = $this->getStatusTime(ParDataEntity::REVOKE_FIELD)) {
      return DrupalDateTime::createFromTimestamp($timestamp);
    }

    return NULL;
  }

  private function getRevocationDateField(): ?DrupalDateTime {
    return $this->hasField('revocation_date') && !$this->get('revocation_date')->isEmpty() ?
      $this->revocation_date?->date :
      NULL;
  }

  /**
   * Sets the date the partnership was revoked.
   *
   * @param ?DrupalDateTime $date
   *   The date this partnership was revoked.
   */
  public function setRevocationDate(DrupalDateTime $date = NULL): void {
    $this->set('revocation_date', $date?->format('Y-m-d'));
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
   * Create a new partnership legal entity wrapper for this partnership.
   *
   * De-duplication checks will be performed to make sure the legal entity
   * being added is not already active on the partnership. It is legitimate,
   * however,
   *
   * @param ParDataLegalEntity $legal_entity
   *  The legal entity to add.
   *
   * @return ParDataPartnershipLegalEntity
   *   Return a new or existing partnership legal entity.
   */
  private function createPartnershipLegalEntity(ParDataLegalEntity $legal_entity): ParDataPartnershipLegalEntity {
    $legal_entity = $legal_entity->deduplicate();

    // Loop through all existing partnership legal entities and check
    // whether an active legal entity is already existing on the partnership.
    $existing_legal_entities = $this->getPartnershipLegalEntities($this->isActive());
    foreach ($existing_legal_entities as $field_delta => $partnership_legal_entity) {
      if ($partnership_legal_entity->getLegalEntity()?->id() === $legal_entity->id()) {
        // Return the existing legal entity if found.
        return $partnership_legal_entity;
      }
    }
    // Create a new partnership legal entity wrapper.
    $partnership_legal_entity = ParDataPartnershipLegalEntity::create([]);
    // Set the legal entity.
    $partnership_legal_entity->setLegalEntity($legal_entity);

    return $partnership_legal_entity;
  }

  /**
   * Get partnership legal entities for this partnership.
   *
   * @param bool $active
   *  If TRUE then only active PLEs are returned. Default FALSE.
   *
   * @return ParDataPartnershipLegalEntity[]
   */
  public function getPartnershipLegalEntities(bool $active = FALSE): array {
    $partnership_legal_entities = $this->get('field_partnership_legal_entity')->referencedEntities();

    // Retain only the active partnership legal entities.
    if ($active) {
      $partnership_legal_entities = array_filter($partnership_legal_entities, function ($partnership_legal_entity) {
        return $partnership_legal_entity->isActive();
      });
    }

    return $partnership_legal_entities;
  }

  /**
   * Get legal entities for this partnership.
   */
  public function getLegalEntity(bool $single = FALSE) {
    $legal_entities = $this->getPartnershipLegalEntities(TRUE);

    // Convert the partnership legal entities into legal entities.
    array_walk($legal_entities, function (&$value) {
      $value = $value->getLegalEntity();
    });

    // Return a single item if requested.
    $legal_entity = !empty($legal_entities) ? current($legal_entities) : NULL;

    return $single ? $legal_entity : $legal_entities;
  }

  /**
   * Add a legal entity to partnership.
   *
   * This includes the wrapping entity and checks to make sure duplicates
   * are not added to the partnership.
   *
   * @param ParDataLegalEntity $legal_entity
   *   A PAR Legal Entity to add.
   *
   * @return ParDataPartnershipLegalEntity
   *   The newly created partnership_legal_entity.
   */
  public function addLegalEntity(ParDataLegalEntity $legal_entity): ParDataPartnershipLegalEntity {
    $partnership_legal_entity = $this->createPartnershipLegalEntity($legal_entity);

    // If the partnership legal entity is existing nothing needs to change.
    if (!$partnership_legal_entity->isNew()) {
      return $partnership_legal_entity;
    }

    // Save the partnership legal entity.
    $partnership_legal_entity->save();

    // Append new partnership legal entity to this partnership.
    $this->get('field_partnership_legal_entity')->appendItem($partnership_legal_entity);

    return $partnership_legal_entity;
  }

  /**
   * Remove a legal entity to partnership.
   *
   * Unlike the other two methods getLegalEntity() and addLegalEntity()
   * this method acts on ParDataPartnershipLegalEntity instances.
   *
   * @param ParDataPartnershipLegalEntity $partnership_legal_entity
   *   A PAR Legal Entity to remove.
   */
  public function removeLegalEntity(ParDataPartnershipLegalEntity $partnership_legal_entity) {
    $partnership_legal_entities = array_filter($this->getPartnershipLegalEntities(), function ($entity) use ($partnership_legal_entity) {
      return $entity->id() === $partnership_legal_entity->id();
    });

    // Remove the field reference.
    foreach ($partnership_legal_entities as $field_delta => $partnership_legal_entity) {
      // Remove the field reference.
      $this->get('field_partnership_legal_entity')->removeItem($field_delta);
    }

    // Save the partnership entity, field references must be removed
    // and saved before the partnership legal entity can be deleted.
    if (!empty($partnership_legal_entities)) {
      $this->save();
    }

    // Delete the partnership legal entity.
    foreach ($partnership_legal_entities as $field_delta => $partnership_legal_entity) {
      $partnership_legal_entity->delete();
    }
  }

  /**
   * Get the names a partnership was previously known by.
   */
  public function setPreviousName(string $name) {
    return $this->get('previous_names')->appendItem($name);
  }

  /**
   * Get the most recent previous name a partnership was known by.
   */
  public function getPreviousName() {
    $previous_names_field = $this->get('previous_names')->filterEmptyItems();
    $main_property_path = $previous_names_field->getFieldDefinition()->getFieldStorageDefinition()->getMainPropertyName();
    $previous_names = $previous_names_field->getValue();

    return $previous_names ? end($previous_names)[$main_property_path] : NULL;
  }

  /**
   * Transfer the partnership to a new authority.
   */
  public function transfer(ParDataAuthority $old, ParDataAuthority $new, DateTimePlus $transfer_date) {
    // Set the record of transfer on the partnership.
    $previous_name = $this->label();
    $this->setPreviousName($previous_name);

    // Create a new revision.
    $message = "The partnership has been transferred from {$old->label()} to {$new->label()}.";
    $revision_message = implode(':', [ParDataPartnership::MEMBER_LIST_REVISION_PREFIX, $message]);
    $this->setNewRevision(TRUE, $revision_message);
    // Set the date of the revision.
    if (!$this->isNew()) {
      $this->setRevisionCreationTime($transfer_date->getTimestamp());
    }

    // Change the authority on the partnership.
    $this->set('field_authority', $new->id());

    // Add the partnership's primary authority contacts to this new authority.
    foreach ($this->getAuthorityPeople() as $person) {
      $new->addPerson($person);
    }
    $new->save();

    // Transfer all pending enquiry types, and set all other types against the old authority.
    $enquiries = array_merge(
      $this->getEnforcements(),
      $this->getDeviationRequests(),
      $this->getInspectionPlanFeedback(),
      $this->getGeneralEnquiry(),
    );
    foreach ($enquiries as $enquiry) {
      $authority = $enquiry->inProgress() ?
        $new : $old;

      $enquiry->setPrimaryAuthority($authority);
      $enquiry->save();
    }
  }

  /**
   * Get the enforcements associated with this partnership.
   *
   * @return ParDataEnforcementNotice[]
   */
  public function getEnforcements(): array {
    // Make sure not to request this more than once for a given entity.
    $enforcements = &drupal_static(__FUNCTION__ . ':' . $this->uuid());
    if (isset($enforcements)) {
      return $enforcements;
    }

    // Get all enforcement notices for this partnership.
    $conditions = [
      'partnership' => [
        'AND' => [
          ['field_partnership', $this->id()],
        ]
      ],
    ];

    // Get the enforcement notices.
    return $this->getParDataManager()->getEntitiesByQuery('par_data_enforcement_notice', $conditions);
  }

  /**
   * Get the deviation requests associated with this partnership.
   *
   * @return ParDataDeviationRequest[]
   */
  public function getDeviationRequests(): array {
    // Make sure not to request this more than once for a given entity.
    $deviation_requests = &drupal_static(__FUNCTION__ . ':' . $this->uuid());
    if (isset($deviation_requests)) {
      return $deviation_requests;
    }

    // Get all deviation requests for this partnership.
    $conditions = [
      'partnership' => [
        'AND' => [
          ['field_partnership', $this->id()],
        ]
      ],
    ];

    // Get the enforcement notices.
    return $this->getParDataManager()->getEntitiesByQuery('par_data_deviation_request', $conditions);
  }

  /**
   * Get the inspection plan feedback associated with this partnership.
   *
   * @return ParDataInspectionFeedback[]
   */
  public function getInspectionPlanFeedback(): array {
    // Make sure not to request this more than once for a given entity.
    $inspection_feedback = &drupal_static(__FUNCTION__ . ':' . $this->uuid());
    if (isset($inspection_feedback)) {
      return $inspection_feedback;
    }

    // Get all inspection feedback for this partnership.
    $conditions = [
      'partnership' => [
        'AND' => [
          ['field_partnership', $this->id()],
        ]
      ],
    ];

    // Get the enforcement notices.
    return $this->getParDataManager()->getEntitiesByQuery('par_data_inspection_feedback', $conditions);
  }

  /**
   * Get the general enquiries associated with this partnership.
   *
   * @return ParDataGeneralEnquiry[]
   */
  public function getGeneralEnquiry(): array {
    // Make sure not to request this more than once for a given entity.
    $general_enquiries = &drupal_static(__FUNCTION__ . ':' . $this->uuid());
    if (isset($general_enquiries)) {
      return $general_enquiries;
    }

    // Get all inspection feedback for this partnership.
    $conditions = [
      'partnership' => [
        'AND' => [
          ['field_partnership', $this->id()],
        ]
      ],
    ];

    // Get the enforcement notices.
    return $this->getParDataManager()->getEntitiesByQuery('par_data_general_enquiry', $conditions);
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

    // Previous names the partnership was known by.
    $fields['previous_names'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Previous names'))
      ->setDescription(t('Any previous names this partnership was known as.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 501,
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
        'region' => 'hidden',
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

    // Member list type.
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
    $fields['member_list_updated'] = BaseFieldDefinition::create('datetime')
      ->setName('member_list_updated')
      ->setLabel(t('Member List Updated'))
      ->setComputed(TRUE)
      ->setClass(ParMemberListUpdatedField::class)
      ->setSettings([
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATE
      ])
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'label' => 'hidden',
        'region' => 'hidden',
        'weight' => 3,
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
    $fields['member_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Member Count'))
      ->setDescription(t('The processed value for the number of coordinated members in this partnership.'))
      ->setComputed(TRUE)
      ->setClass(ParMembersField::class)
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

    // Member link
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
