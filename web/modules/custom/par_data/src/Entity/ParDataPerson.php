<?php

namespace Drupal\par_data\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
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
 *     "storage" = "Drupal\trance\TranceStorage",
 *     "storage_schema" = "Drupal\trance\TranceStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\trance\TranceListBuilder",
 *     "views_data" = "Drupal\par_data\Views\ParDataViewsData",
 *     "form" = {
 *       "default" = "Drupal\trance\Form\ParEntityForm",
 *       "add" = "Drupal\trance\Form\ParEntityForm",
 *       "edit" = "Drupal\trance\Form\ParEntityForm",
 *       "delete" = "Drupal\trance\Form\TranceDeleteForm",
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
   * Get the User account.
   */
  public function getUserAccount() {
    $entities = $this->get('field_user_account')->referencedEntities();
    return $entities ? current($entities) : NULL;
  }

  /**
   * Set the user account.
   *
   * @param mixed $account
   *   Drupal user account.
   */
  public function setUserAccount($account) {
    $this->set('field_user_account', $account);
  }
  /**
   * Get the User account.
   *
   * @param boolean $link_up
   *   Whether or not to link up the accounts if any are found that aren't already linked.
   *
   * @return array
   *   Returns any other PAR Person records if found.
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
   * Get the User accounts that have the same email as this PAR Person.
   *
   * @return mixed|null
   *   Returns a Drupal User account if found.
   */
  public function lookupUserAccount() {
    $entities = \Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties(['mail' => $this->get('email')->getString()]);
    return $entities ? current($entities) : NULL;
  }

  /**
   * Link up the PAR Person to a Drupal User account.
   *
   * @param UserInterface $account
   *   An optional user account to lookup.
   *
   * @return bool|int
   *   If there was an account to link to, that wasn't already linked to.
   */
  public function linkAccounts(UserInterface $account = NULL) {
    $saved = FALSE;
    if (!$account) {
      $account = $this->lookupUserAccount();
    }
    $current_user_account = $this->getUserAccount();
    if ($account && (!$current_user_account || !$account->id() !== $current_user_account->id())) {
      $this->setUserAccount($account);
      $saved = $this->save();
    }
    return $saved ? $account : NULL;
  }

  public function getFullName() {
    return $this->get('first_name')->getString() . ' ' . $this->get('last_name')->getString();
  }

  /**
   * Get any Authorities this user is a member of.
   *
   * @return mixed|null
   *   Returns any business records found.
   */
  public function getBusinesses($entity = NULL) {
    $properties = [
      'type' => ['business'],
      'field_person' => $this->id(),
    ];
    if ($entity) {
      $properties['id'] = $entity->id();
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage('par_data_organisation')
      ->loadByProperties($properties);

    return $entities ? current($entities) : NULL;
  }

  public function isBusinessMember() {
    return (bool) $this->getBusinesses();
  }

  /**
   * Get any Authorities this user is a member of.
   *
   * @return mixed|null
   *   Returns any business records found.
   */
  public function getCoordinators($entity = NULL) {
    $properties = [
      'type' => ['coordinator'],
      'field_person' => $this->id(),
    ];
    if ($entity) {
      $properties['id'] = $entity->id();
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage('par_data_organisation')
      ->loadByProperties($properties);

    return $entities ? current($entities) : NULL;
  }

  public function isCoordinatorMember() {
    return (bool) $this->getCoordinators();
  }

  /**
   * Get any Authorities this user is a member of.
   *
   * @return mixed|null
   *   Returns any business records found.
   */
  public function getAuthorities($entity = NULL) {
    $properties = [
      'type' => ['authority'],
      'field_person' => $this->id(),
    ];
    if ($entity) {
      $properties['id'] = $entity->id();
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage('par_data_authority')
      ->loadByProperties($properties);

    return $entities ? current($entities) : NULL;
  }

  public function isAuthorityMember() {
    return (bool) $this->getAuthorities();
  }

  /**
   * @param string $method_id
   */
  public function setPreferredCommunication($method_id) {
    $methods = $this->getPreferredCommunicationMethods();

    foreach ($methods as $id => $method) {
      if ($method_id === $id) {
        $this->set('communication_' . $id, TRUE);
      }
      else {
        $this->set('communication_' . $id, FALSE);
      }
    } 
  }

  /**
   * Get the preferred communication method.
   *
   * @return string|null
   */
  public function getPreferredCommunicationMethodId() {
    $methods = $this->getPreferredCommunicationMethods();

    foreach ($methods as $method_id => $method) {
      if ($this->get('communication_' . $method_id)->getString()) {
        return $method_id;
      }
    }

    return NULL;
  }

  /**
   * Get the preferred communication method label.
   *
   * @param string $method_id
   *   The id of the method we want the label for.
   *
   * @return int|null
   */
  public function getPreferredCommunicationMethodLabel($method_id) {
    $methods = $this->getPreferredCommunicationMethods();
    return isset($methods[$method_id]) ? $methods[$method_id] : '';
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // First Name.
    $fields['first_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('First Name'))
      ->setDescription(t('The first name of the person.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Last Name.
    $fields['last_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Last Name'))
      ->setDescription(t('The last name of the person.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Work Phone.
    $fields['work_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Work Phone'))
      ->setDescription(t('The work phone of this person.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Email.
    $fields['email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('E-mail'))
      ->setDescription(t('The e-mail address of this person.'))
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
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
