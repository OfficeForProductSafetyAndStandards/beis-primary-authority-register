<?php

namespace Drupal\par_flows\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 */
abstract class ParFlowSubscriberBase implements EventSubscriberInterface {

  /**
   * Get the flow data handler.
   *
   * Note: This will only get the data for the current page, it is not possible
   * to extract the data for anything other than the current route match.
   *
   * @return \Drupal\par_flows\ParFlowDataHandlerInterface
   */
  protected function getFlowDataHandler() {
    return \Drupal::service('par_flows.data_handler');
  }

}
