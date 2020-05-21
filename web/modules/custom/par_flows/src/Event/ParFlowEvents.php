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
  const FLOW_CANCEL = 'par_flows.event_cancel';

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
  const FLOW_BACK = 'par_flows.event_back';

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
  const FLOW_SUBMIT = 'par_flows.event_submit';

}
