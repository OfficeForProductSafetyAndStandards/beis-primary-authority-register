<?php

namespace Drupal\par_subscriptions\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\par_subscriptions\Event\SubscriptionEvent;
use Drupal\par_subscriptions\Event\SubscriptionEvents;

/**
 * Defines the Subscription entity.
 *
 * @ingroup par_subscription
 *
 * @ContentEntityType(
 *   id = "par_subscription",
 *   label = @Translation("PAR Subscription"),
 *   label_singular = @Translation("Subscription"),
 *   label_plural = @Translation("Subscriptions"),
 *   translatable = FALSE,
 *   fieldable = FALSE,
 *   bundle_label = @Translation("PAR Subscription List"),
 *   base_table = "par_subscription",
 *   handlers = {
 *     "views_data" = "Drupal\par_subscriptions\Views\ParSubscriptionsViewsData",
 *   },
 *   admin_permission = "administer par subscribers",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "email" = "email",
 *     "bundle" = "list",
 *   },
 *   bundle_entity_type = "par_subscription_list",
 *   permission_granularity = "bundle",
 * )
 */
class ParSubscription extends ContentEntityBase implements ContentEntityInterface, ParSubscriptionInterface {

  /**
   *
   */
  private function getEventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

  /**
 *
 */
  #[\Override]
  public function getListId() {
    return $this->get('list')->getString();
  }

  /**
 *
 */
  #[\Override]
  public function getListName() {
    return $this->list->entity->label();
  }

  /**
 *
 */
  #[\Override]
  public function getCode() {
    return $this->get('code')->getString();
  }

  /**
 *
 */
  #[\Override]
  public function getEmail() {
    return $this->get('email')->getString();
  }

  /**
 *
 */
  #[\Override]
  public function displayEmail() {
    $email = $this->getEmail();
    $replaceable = substr($email, 1, strpos($email, '@') - 1);

    // Return a partly obfuscated email address.
    return str_replace($replaceable, 'xxxxxx', $email);
  }

  /**
 *
 */
  #[\Override]
  public function isVerified() {
    return $this->get('verified')->get(0)->getValue()['value'] === 1;
  }

  /**
 *
 */
  #[\Override]
  public function subscribe() {
    $this->save();
    $name = SubscriptionEvents::subscribe($this->getListId());
    $event = new SubscriptionEvent($this, $this);
    $this->getEventDispatcher()->dispatch($event, $name);
  }

  /**
 *
 */
  #[\Override]
  public function unsubscribe() {
    $this->delete();
    $name = SubscriptionEvents::unsubscribe($this->getListId());
    $event = new SubscriptionEvent($this, $this);
    $this->getEventDispatcher()->dispatch($event, $name);
  }

  /**
 *
 */
  #[\Override]
  public function verify() {
    $this->set('verified', 1);
    $this->save();
  }

  /**
   * Determines the schema for the base_table property defined above.
   */
  #[\Override]
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Subscription entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Subscription entity.'))
      ->setReadOnly(TRUE);

    // Email field for the subscription.
    $fields['email'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Email'))
      ->setDescription(t('The email address of the user.'))
      ->setSettings([
        'max_length' => 255,
        'not null' => TRUE,
      ]);

    // Subscription list.
    $fields['list'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('List'))
      ->setDescription(t('The entity subscription list.'))
      ->setSetting('target_type', $entity_type->getBundleEntityType())
      ->setRequired(TRUE);

    // Token field for the subscription.
    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setDescription(t('Code to verify and unsubscribe.'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 128,
      ]);

    // Confirmed field for the subscription.
    $fields['verified'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Verified'))
      ->setDescription(t('Whether subscription is verified.'))
      ->setDefaultValue(FALSE);

    // The changed field type automatically updates the timestamp every time the
    // entity is saved.
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the subscription was created.'));

    // The changed field type automatically updates the timestamp every time the
    // entity is saved.
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the subscription was last edited.'));

    return $fields;
  }

}
