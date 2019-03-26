<?php

/**
 * @file
 * Contains Drupal\event_dispatcher_demo\EventSubscriber\ConfigSubscriber.
 */

namespace Drupal\par_invite\EventSubscriber;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\invite\InviteConstants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Url;

class InviteSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  static function getSubscribedEvents() {
    $events['invite_accept'][] = ['onAccept'];
    return $events;
  }

  public function getParDataManager() {
    return \Drupal::service('par_data.manager');
  }

  public function onAccept($event) {
    $event_details = $event->getInviteAcceptEvent();

    // Check that there is a par_data_person for the email address being registered.
    $invite_email = $event_details['invite']->get('field_invite_email_address')->getString();
    $people = $this->getParDataManager()->getEntitiesByProperty('par_data_person', 'email', $invite_email);

    if (empty($people)) {
      $event_details['message'] = $this->t('Sorry, this person has been deleted or the email address changed.');
      $event_details['type'] = 'error';
      $event_details['invite']->setStatus(InviteConstants::INVITE_WITHDRAWN);
      $event_details['invite']->save();
      $event->setInviteAcceptEvent($event_details);
    }

    if ($event_details['type'] === 'error') {
      $path = Url::fromRoute('par_invite.unsuccessful', ['invite' => $event_details['invite']->getRegCode()]);
      $route = new Route($path->toString());
      $event_details['redirect'] = $route;
      $event->setInviteAcceptEvent($event_details);
    }
    // Send the user to the registration page to create a par user account.
    elseif ($event_details['type'] === 'status') {
      $path = Url::fromRoute('par_invite.welcome', ['invite' => $event_details['invite']->getRegCode()]);
      $route = new Route($path->toString());
      $event_details['redirect'] = $route;
      $event->setInviteAcceptEvent($event_details);
    };
  }

}
