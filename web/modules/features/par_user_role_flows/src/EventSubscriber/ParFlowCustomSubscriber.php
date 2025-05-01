<?php

namespace Drupal\par_user_role_flows\EventSubscriber;

use Drupal\par_flows\Event\ParFlowEvents;
use Drupal\par_flows\EventSubscriber\ParFlowSubscriberBase;
use Drupal\par_flows\ParFlowException;
use Drupal\par_flows\Event\ParFlowEventInterface;
use Drupal\Core\Url;


class ParFlowCustomSubscriber extends ParFlowSubscriberBase {

  /**
   * Get the par role manager.
   *
   * @return \Drupal\par_roles\ParRoleManagerInterface
   */
  protected function getParRoleManager() {
    return \Drupal::service('par_roles.role_manager');
  }

  /**
   * The events to react to.
   *
   * @return mixed
   */
  #[\Override]
  static function getSubscribedEvents(): array {
    foreach (ParFlowEvents::getAlLEvents() as $event) {
      $events[$event][] = ['onEvent', 0];
    }
    return $events;
  }

  /**
   * @param ParFlowEventInterface $event
   */
  public function onEvent(ParFlowEventInterface $event) {
    // Ignore if a redirect url has already been found.
    if ($event->getUrl()) {
      return;
    }

    // Only perform this check for these two flows.
    if ($event->getFlow()->id() !== 'manage_user_roles') {
      return;
    }

    // The final route depends on the 'type' parameter in the route.
    $redirect_route = 'par_profile_view_flows.profile';

    // Work out a valid person that is attached to this user to redirect back with.
    $account = $this->getFlowDataHandler()->getParameter('user');
    $people = $account ? $this->getParRoleManager()->getPeople($account) : NULL;
    $person = !empty($people) ? current($people) : NULL;

    try {
      $extra_params = ['par_data_person' => $person];
      $route_params = $event->getFlow()->getRequiredParams($redirect_route, $extra_params);
      $event->setUrl(Url::fromRoute($redirect_route, $route_params));
    } catch (ParFlowException|InvalidParameterException) {

    }
  }

}
