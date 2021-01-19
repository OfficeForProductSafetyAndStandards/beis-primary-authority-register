<?php

namespace Drupal\par_login\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProfilePageRedirectSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $account;

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   *
   * @throws \InvalidArgumentException
   */
  public function __construct(AccountProxyInterface $account) {
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // This needs to run before RouterListener::onKernelRequest(), to ensure
    // the correct route is resolved.
    $events[KernelEvents::REQUEST][] = ['redirectProfilePage', 33];
    return $events;
  }

  /**
   * Redirect requests for the user page to the relevant dashboard.
   *
   * @param GetResponseEvent $event
   * @return void
   */
  public function redirectProfilePage(GetResponseEvent $event) {
    $request = $event->getRequest();

    if ($this->account->isAuthenticated()) {
      $profile_page = Url::fromRoute('entity.user.canonical', ['user' => $this->account->id()]);
    }

    // Compare the page routes and redirect users without permission
    // to view their profile pages.
    if (!$this->account->hasPermission('administer users') &&
      ltrim($request->getPathInfo(), '/') === ltrim($profile_page->getInternalPath(), '/')) {

      $redirect_url = Url::fromRoute('par_dashboards.dashboard');
      $response = new RedirectResponse($redirect_url->toString(), 301);
      $event->setResponse($response);
    }
  }

}
