<?php

namespace Drupal\par_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\Core\Routing\RouteObjectInterface;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\Core\Url;

/**
 *
 */
class ParProgressDestinationSubscriber extends ParFlowSubscriberBase {

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    $events[ParFlowEvents::FLOW_SUBMIT][] = ['onEvent', 300];
    return $events;
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   */
  public function getCurrentRequest() {
    return \Drupal::service('request_stack')->getCurrentRequest();
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Drupal\Core\Path\PathValidatorInterface
   */
  public function getPathValidator() {
    return \Drupal::service('path.validator');
  }

  /**
   * @param \Drupal\par_flows\Event\ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {
    // Ignore if a redirect url has already been found.
    if ($event->getUrl()) {
      return;
    }

    // Only get the current requests query parameters if the routes match,
    // in most cases this should be the case.
    if (!$this->getCurrentRequest()->get(RouteObjectInterface::ROUTE_NAME) ||
      $event->getCurrentRoute()->getRouteName() !== $this->getCurrentRequest()->get(RouteObjectInterface::ROUTE_NAME)) {
      return;
    }
    $query = $this->getCurrentRequest()->query;
    $route_options = [];

    // Determine whether to use the 'destination' query parameter
    // to determine redirection preferences.
    if ($query->has('destination') && $query->has('skip') && (bool) $query->get('skip')) {
      $route_options['query']['destination'] = $query->get('destination');
      $query->remove('destination');
      // The skip parameter can be set as an integer allowing more than one
      // page to be skipped.
      $skip = $query->get('skip');
      if (is_numeric($skip) && (int) $skip > 1) {
        $route_options['query']['skip'] = --$skip;
      }
    }

    // Set any route options.
    $event->setRouteOptions($route_options);

    // Use the destination parameter if it redirects to a route within the flow.
    if ($query->has('destination')) {
      $destination = $query->get('destination');
      $destination_url = $this->getPathValidator()->getUrlIfValid($destination);

      // If the destination parameter is a valid drupal route and it exists in the flow.
      if ($destination_url && $destination_url instanceof Url && $destination_url->isRouted()
        && $event->getFlow()->getStepByRoute($destination_url->getRouteName())) {
        $route_params = $event->getFlow()->getRequiredParams(
          $destination_url->getRouteName(),
          $destination_url->getRouteParameters()
        );
        $url = Url::fromRoute($destination_url->getRouteName(), $route_params);
        $event->setUrl($url);
      }
    }
  }

}
