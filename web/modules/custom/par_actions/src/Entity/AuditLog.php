<?php

namespace Drupal\audit_log\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Audit log entity.
 *
 * @ingroup audit_log
 *
 * @ContentEntityType(
 *   id = "audit_log",
 *   label = @Translation("Audit log"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\audit_log\Entity\AuditLogViewsData",
 *     "form" = {
 *       "default" = "Drupal\audit_log\Form\AuditLogForm",
 *     },
 *     "access" = "Drupal\audit_log\AuditLogAccessControlHandler",
 *   },
 *   base_table = "audit_log",
 *   admin_permission = "administer audit log entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   }
 * )
 */
class AuditLog extends ContentEntityBase implements AuditLogInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getMessage();
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('entity_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEvent() {
    return $this->get('event')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->get('message')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getDate() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Audit log entity.'))
      ->setSetting('target_type', 'user');

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Target entity'))
      ->setDescription(t('The entity id of the entity that was created, modified or deleted.'));

    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Target entity type'))
      ->setDescription(t('The target entity type.'));

    $fields['event'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Event'))
      ->setDescription(t('The event type, usually insert, update or delete.'));

    $fields['previous_state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Previous state'))
      ->setDescription(t('The previous state of the entity if available.'));

    $fields['current_state'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Current state'))
      ->setDescription(t('The current state of the entity if available.'));

    $fields['message'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Text of log message'))
      ->setDescription(t('Text of log message to be passed into the t() function.'));

    $fields['variables'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Variables of log message'))
      ->setDescription(t('Serialized array of variables that match the message string and that is passed into the t() function.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
