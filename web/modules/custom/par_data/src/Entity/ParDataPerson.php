<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_data\ParDataRelationship;
use Drupal\par_flows\ParFlowException;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Defines the par_data_person entity.
 *
 * @ingroup par_data
 *
 * @ContentEntityType(
 *   id = "par_data_person",
 *   label = @Translation("PAR Person"),
 *   label_collection = @Translation("PAR People"),
 *   label_singular = @Translation("PAR Person"),
 *   label_plural = @Translation("PAR People"),
 *   label_count = @PluralTranslation(
 *     singular = "@count person",
 *     plural = "@count people"
 *   ),
 *   bundle_label = @Translation("PAR Person type"),
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
 *   base_table = "par_people",
 *   data_table = "par_people_field_data",
 *   revision_table = "par_people_revision",
 *   revision_data_table = "par_people_field_revision",
 *   admin_permission = "administer par_data_person entities",
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
 *     "collection" = "/admin/content/par_data/par_data_person",
 *     "canonical" = "/admin/content/par_data/par_data_person/{par_data_person}",
 *     "edit-form" = "/admin/content/par_data/par_data_person/{par_data_person}/edit",
 *     "delete-form" = "/admin/content/par_data/par_data_person/{par_data_person}/delete"
 *   },
 *   bundle_entity_type = "par_data_person_type",
 *   permission_granularity = "bundle",
 *   field_ui_base_route = "entity.par_data_person_type.edit_form"
 * )
 */
class ParDataPerson extends ParDataEntity implements ParDataPersonInterface {

  /**
   * {@inheritdoc}
   */
  public function filterRelationshipsByAction($relationship, $action) {
    switch ($action) {
      case 'manage':
        // Only follow relationships to authorities and organisations.
        // This is the very core of how membership is granted within PAR.
        return (bool) ($relationship->getEntity()->getEntityTypeId() === 'par_data_organisation'
          || $relationship->getEntity()->getEntityTypeId() === 'par_data_authority');

    }

    return parent::filterRelationshipsByAction($relationship, $action);
  }

  /**
   * {@inheritdoc}
   *
   * Internal function only to get the correct user account for a person.
   *
   * @see self::getUserAccount()
   */
  public function retrieveUserAccount() {
    $entities = $this->get('field_user_account')->referencedEntities();

    return $entities ? current($entities) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function lookupUserAccount() {
    $entities = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties(['mail' => $this->get('email')->getString()]);

    return $entities ? current($entities) : NULL;
  }

  /**
   * Determine whether the person has a user account set.
   *
   * Does not include whether a user account can looked up by matching an email
   * address @see self::lookupUserAccount().
   *
   * @see self::getUserAccount()
   *
   * @return bool
   *   Whether a user account has been set.
   */
  public function hasUserAccount() {
    return $this->hasField('field_user_account')
      && !$this->get('field_user_account')->isEmpty()
      && !empty($this->get('field_user_account')->referencedEntities());
  }

  /**
   * {@inheritdoc}
   *
   * A person can be matched to a user account if:
   * a) the user id is set on the field_user_account
   * b) the user account is not set (as above) but the email matches the user account email
   * @see ParDataManager::getUserPeople()
   */
  public function getUserAccount() {
    return $this->hasUserAccount() ?
      $this->retrieveUserAccount() : $this->lookupUserAccount();
  }

  /**
   * {@inheritdoc}
   */
  public function setUserAccount($account) {
    $this->set('field_user_account', $account);
  }

  /**
   * {@inheritdoc}
   */
  public function getSimilarPeople($link_up = TRUE) {
    $account = $this->getUserAccount();

    // Link this entity to the Drupal User if one exists.
    if ($link_up && $account && !$this->hasUserAccount()) {
      $account = $this->linkAccounts();
    }

    // Get the dominant email address, for people with a user account this is
    // the user account email, for all others it's the email of the person.
    $email = $account instanceof UserInterface ? $account->getEmail() : $this->getEmail();

    // Get the entity query.
    $query = $this->entityTypeManager()
      ->getStorage($this->getEntityTypeId())
      ->getQuery('OR');

    $query->condition('email', $email, '=');

    // If there is an account we can search for people liked to this account also.
    if ($account) {
      $query->condition('field_user_account', $account->id(), 'IN');
    }

    $results = $query->execute();
    $people = $this->entityTypeManager()
      ->getStorage($this->getEntityTypeId())
      ->loadMultiple(array_unique($results));

    // Do not return people that are already linked to a different user account.
    $people = array_filter($people, function ($person) use ($account) {
      if (!$person->hasUserAccount()) {
        return TRUE;
      }
      if ($account && $person->retrieveUserAccount()->id() === $account->id()) {
        return TRUE;
      }

      return FALSE;
    });

    return isset($people) ? $people : [];
  }

  /**
   * {@inheritdoc}
   */
  public function linkAccounts(UserInterface $account = NULL) {
    $saved = FALSE;
    if (!$account) {
      $account = $this->lookupUserAccount();
    }

    $current_user_account = $this->retrieveUserAccount();
    if ($account && (!$current_user_account || $account->id() !== $current_user_account->id())) {
      // Add the user account to this person.
      $this->setUserAccount($account);
      $saved = $this->save();
    }

    return $saved ? $account : NULL;
  }

  /**
   * Updates the person email address, and the user account if there isn't another person registered to it.
   *
   * @param string $email
   *   The email address to update.
   * @param User $account
   */
  public function updateEmail($email, User &$account = NULL) {
    $this->set('email', $email);

    if (!$account) {
      $account = $this->lookupUserAccount();
    }
    if ($account) {
      $account->setEmail($email);
    }
  }

  /**
   * A helper function to save this person to the correct authorities.
   *
   * @param $authorities
   *   A list of authority IDs to save.
   * @param bool $save
   *
   * @return array
   *   An array of updated authorities.
   */
  public function updateAuthorityMemberships($authorities, $save = FALSE) {
    $authorities = NestedArray::filter((array) $authorities);
    $unset = [];

    $user = User::load(\Drupal::currentUser()->id());
    $relationships = $this->getRelationships('par_data_authority');
    if ($user->hasPermission('bypass par_data membership')) {
      foreach ($relationships as $relationship) {
        $id = $relationship->getEntity()->id();

        // Unset any relationships that are not selected.
        if (!array_search($id, $authorities)) {
          $unset[] = $id;
        }
      }
    }
    else {
      $user_authorities = $this->getParDataManager()
        ->hasMembershipsByType($user, 'par_data_authority');
      $user_authorities_ids = $this->getParDataManager()
        ->getEntitiesAsOptions($user_authorities);

      foreach ($relationships as $relationship) {
        $id = $relationship->getEntity()->id();

        // Any existing relationships that the current user is
        // not allowed to update should not be removed.
        if (!isset($user_authorities_ids[$id]) && !array_search($id, $authorities)) {
          $authorities[] = $id;
        }
        // Any existing relationships that the current user is
        // allowed to update but that have been excluded should be removed.
        if (isset($user_authorities_ids[$id]) && !array_search($id, $authorities)) {
          $unset[] = $id;
        }
      }
    }

    // Add this person to any authorities.
    $authorities = ParDataAuthority::loadMultiple(array_unique($authorities));
    foreach ($authorities as $authority) {
      $referenced_ids = array_column($authority->get('field_person')->getValue(), 'target_id');
      // Check that we're not adding a duplicate.
      $search = array_search($this->id(), $referenced_ids);
      if (!$search) {
        $authority->get('field_person')->appendItem([
          'target_id' => $this->id(),
        ]);
      }

      if ($save) {
        $authority->save();
      }
    }

    // Remove this person from any authorities.
    if ($save) {
      $removed_authorities = isset($unset) ? ParDataAuthority::loadMultiple(array_unique($unset)) : [];
      foreach ($removed_authorities as $authority) {
        $referenced_ids = array_column($authority->get('field_person')->getValue(), 'target_id');
        // For some insanely annoying reason the field re-counts the index
        // on removing an item so performing this in reverse ensures none
        // of the remaining keys queued for deletion will get re-counted.
        $keys = array_reverse(array_keys($referenced_ids, $this->id()));
        if ($keys) {
          foreach ($keys as $key) {
            if ($authority->get('field_person')->offsetExists($key)) {
              $authority->get('field_person')->removeItem($key);
            }
          }
          $authority->save();
        }

      }
    }

    return $authorities;
  }

  /**
   * A helper function to save this person to the correct organisations.
   *
   * @param $organisations
   *   A list of organisation IDs to save.
   * @param bool $save
   *
   * @return array
   *   An array or updated organisations.
   */
  public function updateOrganisationMemberships($organisations, $save = FALSE) {
    $organisations = NestedArray::filter((array) $organisations);
    $unset = [];

    $user = User::load(\Drupal::currentUser()->id());
    $relationships = $this->getRelationships('par_data_organisation');
    if ($user->hasPermission('bypass par_data membership')) {
      foreach ($relationships as $relationship) {
        $id = $relationship->getEntity()->id();

        // Unset any relationships that are not selected.
        if (!array_search($id, $organisations)) {
          $unset[] = $id;
        }
      }
    }
    else {
      $user_organisations = $this->getParDataManager()
        ->hasMembershipsByType($user, 'par_data_organisation');
      $user_organisations_ids = $this->getParDataManager()
        ->getEntitiesAsOptions($user_organisations);

      foreach ($relationships as $relationship) {
        $id = $relationship->getEntity()->id();

        // Any existing relationships that the current user is
        // not allowed to update should be retained.
        if (!isset($user_organisations_ids[$id]) && !array_search($id, $organisations)) {
          $organisations[] = $id;
        }
        // Any existing relationships that the current user is
        // allowed to update but that have not been selected should be removed.
        if (isset($user_organisations_ids[$id]) && !array_search($id, $organisations)) {
          $unset[] = $id;
        }
      }
    }

    $organisations = ParDataOrganisation::loadMultiple(array_unique($organisations));
    foreach ($organisations as $organisation) {
      $referenced_ids = array_column($organisation->get('field_person')->getValue(), 'target_id');
      // Check that we're not adding a duplicate.
      $search = array_search($this->id(), $referenced_ids);
      if (!$search) {
        $organisation->get('field_person')->appendItem([
          'target_id' => $this->id(),
        ]);
      }

      if ($save) {
        $organisation->save();
      }

    }

    // Remove this person from any organisations.
    if ($save) {
      $removed_organisations = isset($unset) ? ParDataOrganisation::loadMultiple(array_unique($unset)) : [];
      foreach ($removed_organisations as $organisation) {
        $referenced_ids = array_column($organisation->get('field_person')->getValue(), 'target_id');
        // For some insanely annoying reason the field re-counts the index
        // on removing an item so performing this in reverse ensures none
        // of the remaining keys queued for deletion will get re-counted.
        $keys = array_reverse(array_keys($referenced_ids, $this->id()));
        if ($keys) {
          foreach ($keys as $key) {
            if ($organisation->get('field_person')->offsetExists($key)) {
              $organisation->get('field_person')->removeItem($key);
            }
          }
          $organisation->save();
        }
      }
    }

    return $organisations;
  }

  /**
   * Get PAR Person's full name.
   *
   * @return string
   *   Their full name including title/salutation field.
   */
  public function getFullName() {
    return implode(" ", [
      $this->get('salutation')->getString(),
      $this->getFirstName(),
      $this->getLastName(),
    ]);
  }

  /**
   * Get PAR Person's first name.
   *
   * @return string
   *   Their first name.
   */
  public function getFirstName() {
    return $this->get('first_name')->getString();
  }

  /**
   * Get PAR Person's first name.
   *
   * @return string
   *   Their first name.
   */
  public function getLastName() {
    return $this->get('last_name')->getString();
  }

  /**
   * Get PAR Person's work phone pseudo-field value.
   *
   * @return string
   *   PAR Person's work phone including preference text.
   */
  public function getWorkPhone() {
    return $this->getCommunicationFieldText(
      $this->get('work_phone')->getString(),
      'communication_phone'
    );
  }

  /**
   * Get PAR Person's mobile phone pseudo-field value.
   *
   * @return string
   *   PAR Person's mobile phone including preference text.
   */
  public function getMobilePhone() {
    return $this->getCommunicationFieldText(
      $this->get('mobile_phone')->getString(),
      'communication_mobile'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->get('email')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getEmailWithPreferences() {
    return $this->getCommunicationFieldText(
      $this->get('email')->getString(),
      'communication_email'
    );
  }

  /**
   * Get PAR Person's email pseudo-field value.
   *
   * @return string
   *   PAR Person's email mailto link including preference text.
   */
  public function getEmailLink() {
    $email = $this->getEmail();

    $email_link = Link::fromTextAndUrl($email,
      Url::fromUri("mailto:{$email}"));

    $email_link_safe = Xss::filter($email_link->toString(), ['a']);

    return $this->getCommunicationFieldText(
      $email_link_safe,
      'communication_email'
    );
  }

  /**
   * Helper function to format communication pseudo field value.
   *
   * @param string $text
   *   Text to display.
   * @param string $preference_field
   *   Preference field machine name.
   * @return string
   *   Pseudo field value.
   */
  public function getCommunicationFieldText($text, $preference_field) {
    if ($preference_message = $this->getCommunicationPreferredText($preference_field)) {
      return "{$text} ({$preference_message})";
    }

    return $text;
  }

  /**
   * Helper function to get preference field boolean "on" value.
   *
   * @param string $preference_field
   *   Preference field id.
   * @return string|null
   *   Preference field boolean value label text.
   */
  public function getCommunicationPreferredText($preference_field) {
    if ($this->get($preference_field)->getString() == 1) {
      $preference_message = "preferred";
    }

    return isset($preference_message) ? $preference_message : null;
  }

  /**
   * Get the notification preferences.
   *
   * @param string $notification_type
   *   The \Drupal\message\Entity\MessageTemplate::id() that indicates the notification type.
   *
   * @return bool
   *   Whether or not the person has choosen to receive additional notifications.
   */
  public function getNotificationPreferences() {
    $notification_preferences = !$this->get('field_notification_preferences')->isEmpty() ?
      $this->get('field_notification_preferences')->referencedEntities() : NULL;

    return $notification_preferences;
  }

  /**
   * Get the notification preferences.
   *
   * @param string $notification_type
   *   The \Drupal\message\Entity\MessageTemplate::id() that indicates the notification type.
   *
   * @return bool
   *   Whether or not the person has chosen to receive additional notifications.
   */
  public function hasNotificationPreference($notification_type) {
    $notification_preferences = $this->getNotificationPreferences();

    if ($notification_preferences) {
      $notification_preferences = array_filter($notification_preferences, function ($preference) use ($notification_type) {
        return ($notification_type === $preference->getTemplate());
      });
    }
    else {
      $notification_preferences = NULL;
    }

    return (!empty($notification_preferences));
  }

  public function getReferencedLocations() {
    $locations = [];
    $relationships = $this->getRelationships(NULL, NULL, TRUE);

    // Get all the relationships that reference this person.
    $relationships = array_filter($relationships, function ($relationship) {
      return (ParDataRelationship::DIRECTION_REVERSE === $relationship->getRelationshipDirection());
    });

    foreach ($relationships as $relationship) {
      $label = '';

      switch ($relationship->getId()) {
        case 'par_data_organisation:field_person':
          $label .= 'Contact at the organisation: ';

          break;

        case 'par_data_authority:field_person':
          $label .= 'Contact at the authority: ';

          break;
        case 'par_data_partnership:field_organisation_person':
          $label .= 'Primary contact for the organisation: ';

          break;

        case 'par_data_partnership:field_authority_person':
          $label .= 'Primary contact for the authority: ';

          break;

        case 'par_data_general_enquiry:field_person':
          $label .= 'General enquiry for: ';

          break;

        case 'par_data_deviation_request:field_person':
          $label .= 'Deviation request for: ';

          break;

        case 'par_data_inspection_feedback:field_person':
          $label .= 'Inspection feedback for: ';

          break;

        case 'par_data_enforcement_notice:field_person':
          $label .= 'Enforcement notice for: ';

          break;
      }

      $label = ucfirst($label . $relationship->getEntity()->label());
      $locations[] = $label;
    }

    return $locations;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Title.
    $fields['salutation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of this person.'))
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

    // First Name.
    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription(t('The first name of the person.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
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

    // Last Name.
    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription(t('The last name of the person.'))
      ->addConstraint('par_required')
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
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

    // Job title.
    $fields['job_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Job title'))
      ->setDescription(t('The job title of the person.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Work Phone.
    $fields['work_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Work Phone'))
      ->setDescription(t('The work phone of this person.'))
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

    // Mobile Phone.
    $fields['mobile_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Mobile Phone'))
      ->setDescription(t('The mobile phone of this person.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Email.
    $fields['email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('E-mail'))
      ->setDescription(t('The e-mail address of this person.'))
      ->setRequired(TRUE)
      ->addPropertyConstraints('value', [
        'Email' => ['message' => 'You must enter an email address in a valid format.'],
      ])
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 500,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 7,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Communication by Email.
    $fields['communication_email'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Communication by E-mail'))
      ->setDescription(t('Whether to allow contact by e-mail for this partnership.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 8,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Communication by Phone.
    $fields['communication_phone'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Communication by Work Phone'))
      ->setDescription(t('Whether to allow contact by work phone for this partnership.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 9,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Communication by Mobile.
    $fields['communication_mobile'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Communication by Mobile Phone'))
      ->setDescription(t('Whether to allow contact by mobile for this partnership.'))
      ->setRevisionable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Communication Notes.
    $fields['communication_notes'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Communication Notes'))
      ->setDescription(t('Additional notes and communication preferences for this partnership.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'text_processing' => 0,
      ])->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 11,
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
