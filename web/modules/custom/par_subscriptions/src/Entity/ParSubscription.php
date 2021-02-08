<?php

namespace Drupal\par_subscriptions\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

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
class ParSubscription extends ContentEntityBase implements ContentEntityInterface {

  /**
   * Determines the schema for the base_table property defined above.
   */
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
