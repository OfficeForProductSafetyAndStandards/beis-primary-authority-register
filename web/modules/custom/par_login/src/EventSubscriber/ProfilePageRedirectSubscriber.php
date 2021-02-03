<?php

namespace Drupal\par_login\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Psr\Log\LoggerInterface;
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
   * The logger service.
   *
   * @param \Psr\Log\LoggerInterface $logger
   */
  private $logger;

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   *
   * @throws \InvalidArgumentException
   */
  public function __construct(AccountProxyInterface $account, LoggerInterface $logger) {
    $this->account = $account;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // This MUST run before RouterListener::onKernelRequest(), to ensure
    // the redirection doesn't recursively resolve.
    $events[KernelEvents::REQUEST][] = ['redirectProfilePage', 34];
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
    if ($this->account->isAuthenticated() && $profile_page &&
      !$this->account->hasPermission('administer users') &&
      ltrim($request->getPathInfo(), '/') === ltrim($profile_page->getInternalPath(), '/')) {

      // Log the denied request as well as the referer for information.
      $referer = $request->headers->get('referer');
      $this->logger->warning(
        'User @user is not allowed to access their profile page. Redirected from %redirected',
        ['%user' => $this->account->id(), '%redirected' => $referer]
      );

      // Determine which dashboard the user should be directed to.
      if ($this->account->hasPermission('access helpdesk')) {
        $dashboard = 'par_help_desks_flows.helpdesk_dashboard';
      }
      else if ($this->account->hasPermission('access par dashboard')) {
        $dashboard = 'par_dashboards.dashboard';
      }
      if (isset($dashboard)) {
        $redirect_url = Url::fromRoute($dashboard);
        $response = new RedirectResponse($redirect_url->toString(), 301);
        $event->setResponse($response);
      }
    }
  }

}
