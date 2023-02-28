<?php

namespace Drupal\par_notification\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a PAR Notification Subscriber plugin annotation object.
 *
 * @see \Drupal\par_notification\ParSubscriptionManager
 * @see plugin_api
 *
 * @Annotation
 */
class ParMessageSubscriber extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The status of the plugin.
   *
   * This is an easy way to turn off plugins.
   *
   * @var bool
   */
  public $status = TRUE;

  /**
   * The messages that this plugin will get subscribers for.
   *
   * @var array
   */
  public $message;

  /**
   * An array of rules that can apply to this message, one of:
   *  - rule-based
   *  - user-preference-based
   *  - membership-based
   *
   * @var array
   */
  public $rules;

}
