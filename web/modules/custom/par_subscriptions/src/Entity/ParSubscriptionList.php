<?php

namespace Drupal\par_subscriptions\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the subscription list.
 *
 * @ConfigEntityType(
 *   id = "par_subscription_list",
 *   label = @Translation("PAR Subscription List"),
 *   config_prefix = "par_subscription_list",
 *   bundle_of = "par_subscription",
 *   admin_permission = "administer all subscribers",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "verification",
 *   }
 * )
 */
class ParSubscriptionList extends ConfigEntityBase {

  use StringTranslationTrait;

  /**
   * The subscription list ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The subscription list label.
   *
   * @var string
   */
  protected $label;

  /**
   * The subscription list description.
   *
   * @var string
   */
  protected $description;

  /**
   * Whether subscribers need to verify themselves.
   *
   * @var bool
   */
  protected $verification;

  /**
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  public function getEventDispatcher() {
    return \Drupal::service('event_dispatcher');
  }

}
