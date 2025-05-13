<?php

namespace Drupal\par_login\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
class ProfilePageRedirectSubscriber implements EventSubscriberInterface {

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   *
   * @throws \InvalidArgumentException
   */
  public function __construct(
    /**
     * The current user.
     */
    private readonly AccountProxyInterface $account,
    private readonly LoggerInterface $logger,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public static function getSubscribedEvents(): array {
    // This MUST run before RouterListener::onKernelRequest(), to ensure
    // the redirection doesn't recursively resolve.
    $events[KernelEvents::REQUEST][] = ['redirectProfilePage', 34];
    return $events;
  }

  /**
   * Redirect requests for the user page to the relevant dashboard.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *
   * @return void
   */
  public function redirectProfilePage(RequestEvent $event) {
    $request = $event->getRequest();

    if ($this->account->isAuthenticated()) {
      $profile_page = Url::fromRoute('entity.user.canonical', ['user' => $this->account->id()]);
    }

    // Compare the page routes and redirect users without permission
    // to view their profile pages.
    if ($this->account->isAuthenticated() && $profile_page &&
      !$this->account->hasPermission('administer users') &&
      trim($request->getPathInfo(), '/') === trim($profile_page->getInternalPath(), '/')) {

      // Log the denied request as well as the referer for information.
      $referer = $request->headers->get('referer');
      $this->logger->info(
        'User @user is not allowed to access their profile page. Redirected from %redirected',
        ['%user' => $this->account->id(), '%redirected' => $referer]
      );

      // Determine which dashboard the user should be directed to.
      if ($this->account->hasPermission('access helpdesk')) {
        $dashboard = 'par_help_desks_flows.helpdesk_dashboard';
      }
      elseif ($this->account->hasPermission('access par dashboard')) {
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
