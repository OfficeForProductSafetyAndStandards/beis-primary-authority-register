<?php

namespace Drupal\par_user_block_flows\EventSubscriber;

use Drupal\Core\Url;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\EventSubscriber\ParFlowSubscriberBase;
use Drupal\par_flows\ParFlowException;

/**
 * {@inheritdoc}
 */
class ParFlowCustomSubscriber extends ParFlowSubscriberBase {

  /**
   * Get the par data manager.
   */
  protected function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  /**
   * The events to react to.
   */
  public static function getSubscribedEvents() {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', 0];
    }
    return $events;
  }

  /**
   * The events action.
   */
  public function onEvent(ParFlowEventInterface $event) {
    // Ignore if a redirect url has already been found.
    if ($event->getUrl()) {
      return;
    }

    // Only perform this check for these two flows.
    if ($event->getFlow()->id() !== 'block_user' && $event->getFlow()->id() !== 'unblock_user') {
      return;
    }

    // The final route depends on the 'type' parameter in the route.
    $redirect_route = 'par_profile_view_flows.profile';

    // Get valid person that is attached to this user to redirect back with.
    $account = $this->getFlowDataHandler()->getParameter('user');
    $people = $account ? $this->getParDataManager()->getUserPeople($account) : NULL;
    $person = $people ? current($people) : NULL;

    try {
      $extra_params = ['par_data_person' => $person];
      $route_params = $event->getFlow()->getRequiredParams($redirect_route, $extra_params);
      $event->setUrl(Url::fromRoute($redirect_route, $route_params));
    }
    catch (ParFlowException $e) {

    }
  }

}
