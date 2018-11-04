<?php

namespace Drupal\par_data\Entity;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_data\ParDataRelationship;
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
class ParDataPerson extends ParDataEntity {

  /**
   * {@inheritdoc}
   */
  public function getUserAccount() {
    $entities = $this->get('field_user_account')->referencedEntities();
    return $entities ? current($entities) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrLookupUserAccount() {
    $account = $this->getUserAccount();

    // Lookup the user account if one could not be saved.
    if (!$account) {
      $account = $this->lookupUserAccount();
    }

    return $account;
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
    if (!$account && $link_up) {
      $account = $this->linkAccounts();
    }

    // Return all similar people.
    if ($account) {
      $accounts = \Drupal::entityTypeManager()
        ->getStorage($this->getEntityTypeId())
        ->loadByProperties(['email' => $account->get('mail')->getString()]);
    }

    return isset($accounts) ? $accounts : [];
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
   * {@inheritdoc}
   */
  public function linkAccounts(UserInterface $account = NULL) {
    $saved = FALSE;
    if (!$account) {
      $account = $this->lookupUserAccount();
    }
    $current_user_account = $this->getUserAccount();
    if ($account && (!$current_user_account || $account->id() !== $current_user_account->id())) {
      // Add the user account to this person.
      $this->setUserAccount($account);
      $saved = $this->save();
    }

    return $saved ? $account : NULL;
  }

  /**
   * Merge all user accounts that share the same e-mail address.
   */
  public function mergePeople() {
    $uids = [];

    // Foreach person record with the same email lookup any other
    // entity which references it and update the reference to the
    // new person record.
    /** @var ParDataPerson[] $people */
    $people = $this->getParDataManager()->getEntitiesByProperty('par_data_person', 'email', $this->getEmail());
    foreach ($people as $person) {
      // Skip this entity, this is the one we'll leave.
      if ($person->id() === $this->id()) {
        continue;
      }

      $referenced_users = $person->get('field_user_account')->referencedEntities();
      foreach ($referenced_users as $referenced_user) {
        if (!isset($uids[$referenced_user->id()])) {
          $uids[$referenced_user->id()] = $referenced_user;
        }
      }

      $relationships = $person->getRelationships(NULL, NULL, TRUE);
      foreach ($relationships as $relationship) {
        // Update all entities that reference the soon to be merged person.
        if ($relationship->getRelationshipDirection() === ParDataRelationship::DIRECTION_REVERSE) {
          // Only update the related entity if it does not already reference the updated record.
          $update = TRUE;
          foreach ($relationship->getEntity()->get($relationship->getField()->getName())->referencedEntities() as $e) {
            if ($e->id() === $this->id()) {
              $update = FALSE;
            }
          }

          if ($update) {
            $relationship->getEntity()->get($relationship->getField()->getName())->appendItem($this->id());
            $relationship->getEntity()->save();
          }
        }
      }

      // Remove this person record.
      $person->delete();
    }

    // Be sure to make sure that all referenced uids on old person
    // records are transferred.
    foreach ($uids as $uid) {
      $update = TRUE;
      foreach ($this->get('field_user_account')->referencedEntities() as $e) {
        if ($e->id() === $this->id()) {
          $update = FALSE;
        }
      }
      if ($update) {
        $this->get('field_user_account')->appendItem($uid);
      }
    }

    // This method will always save the entity.
    $this->save();
  }

  /**
   * Updates the person email address, and the user account if there isn't another person registered to it.
   *
   * @param string $email
   *   The email address to update.
   * @param User $account
   */
  public function updateEmail($email, User &$account) {
    $this->set('email', $email);

    if (!$account) {
      $account = $this->lookupUserAccount();
    }
    if ($account) {
      $account->setEmail($email);
    }
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
      $this->get('first_name')->getString(),
      $this->get('last_name')->getString(),
    ]);
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
   *   Whether or not the person has choosen to receive additional notifications.
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
      ->addConstraint('par_required')
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
