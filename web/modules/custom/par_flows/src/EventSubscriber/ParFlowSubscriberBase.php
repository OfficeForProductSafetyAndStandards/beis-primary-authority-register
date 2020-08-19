<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\par_flows\Event\ParFlowEvent;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


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


