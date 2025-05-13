<?php

namespace Drupal\par_person_create_flows\EventSubscriber;

use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\EventSubscriber\ParFlowSubscriberBase;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\Core\Url;

/**
 *
 */
class ParFlowCustomSubscriber extends ParFlowSubscriberBase {

  /**
   * Get the par data manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   */
  protected function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

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
   * @param \Drupal\par_flows\Event\ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {
    // Ignore if a redirect url has already been found.
    if ($event->getUrl()) {
      return;
    }

    // Only perform this check for these two flows.
    if ($event->getFlow()->id() !== 'person_create') {
      return;
    }

    // The final route depends on the 'type' parameter in the route.
    $redirect_route = 'par_profile_view_flows.profile';

    // Work out a valid person that is attached to this user to redirect back with.
    $person = $this->getFlowDataHandler()->getParameter('par_data_person');

    if ($person instanceof ParDataPersonInterface) {
      try {
        $extra_params = ['par_data_person' => $person->id()];
        $route_params = $event->getFlow()
          ->getRequiredParams($redirect_route, $extra_params);
        $event->setUrl(Url::fromRoute($redirect_route, $route_params));
      }
      catch (ParFlowException) {

      }
    }
  }

}
