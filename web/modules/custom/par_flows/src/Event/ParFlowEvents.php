<?php

namespace Drupal\par_flows\Event;

/**
 * Defines events for the par_flows module.
 *
 * It is best practice to define the unique names for events as constants on a class.
 * This provides a place for documentation of the events. As well as allowing the event dispatcher
 * to use the constants instead of hard coding a string.
 *
 */
final class ParFlowEvents {

  /**
   * Name of the event fired when a journey is started.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_START = 'par_flows.start';

  /**
   * Name of the event fired when a form action is fired.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_CUSTOM_ACTION = 'par_flows.event_custom_action';

  /**
   * Name of the event fired when a journey is canceled.
   *
   * This event allows modules to perform an action whenever a journey on the register is canceled via the forms
   * The event listener method receives a \Drupal\par_flows\Event\ParFlowEven instance.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_CANCEL = 'par_flows.event_cancel';

  /**
   * Name of the event fired when a journey is completed.
   *
   * This event allows modules to perform an action whenever a journey on the register is completed via the forms
   * The event listener method receives a \Drupal\par_flows\Event\ParFlowEven instance.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_DONE = 'par_flows.event_done';

  /**
   * Name of the event fired when the back button is triggered on a journey.
   *
   * This event allows modules to perform an action whenever a back process is triggered on a journey within the register.
   * The event listener method receives a \Drupal\par_flows\Event\ParFlowEven instance.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_BACK = 'par_flows.event_back';

  /**
   * Name of the event fired when a form is submitted on a journey.
   *
   * This event allows modules to perform an action whenever a journey form on the register submitted is.
   * The event listener method receives a \Drupal\par_flows\Event\ParFlowEven instance.
   *
   * @Event
   *
   * @see \Drupal\par_flows\Event\ParFlowEvent
   *
   * @var string
   */
  const string FLOW_SUBMIT = 'par_flows.event_submit';

  public static function getAlLEvents() {
    return [
      self::FLOW_CANCEL,
      self::FLOW_DONE,
      self::FLOW_BACK,
      self::FLOW_SUBMIT
    ];
  }

  /**
   * Static method for generating an event name based on the operation.
   */
  public static function getEventByAction($operation) {
    // Return event names for specific operations.
    return match ($operation) {
        'back' => ParFlowEvents::FLOW_BACK,
        'done' => ParFlowEvents::FLOW_DONE,
        'cancel' => ParFlowEvents::FLOW_CANCEL,
        default => ParFlowEvents::FLOW_SUBMIT,
    };
  }

}
