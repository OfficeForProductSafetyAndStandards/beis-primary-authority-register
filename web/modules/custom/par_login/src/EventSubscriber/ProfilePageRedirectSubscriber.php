<?php

namespace Drupal\par_login\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProfilePageRedirectSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => [['redirectProfilePage']]
    ];
  }

  /**
   * Redirect requests for my_content_type node detail pages to node/123.
   *
   * @param GetResponseEvent $event
   * @return void
   */
  public function redirectProfilePage(GetResponseEvent $event) {
    $request = $event->getRequest();

    // Redirect only for the user profile page.
    if ($request->attributes->get('_route') !== 'entity.user.canonical') {
      return;
    }

    $redirect_url = Url::fromRoute('par_dashboards.dashboard');
    $response = new RedirectResponse($redirect_url->toString(), 301);
    $event->setResponse($response);
  }

}
