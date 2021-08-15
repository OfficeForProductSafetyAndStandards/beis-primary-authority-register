<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Check if a deviation request has already been approved.
 *
 * @ParLinkAction(
 *   id = "unauthenticated_user",
 *   title = @Translation("User is not logged in."),
 *   status = TRUE,
 *   weight = -99,
 *   destination = "/user/login"
 * )
 */
class ParUnauthenticatedUser extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    // If the user is not logged in, allow them to login first.
    if (!$this->user->isAuthenticated()) {
      // Redirect to user login page with the appended destination query.
      $signin_url = Url::fromRoute(
        'par_notification.link_access_denied',
        ['message' => $message->id()],
        $this->getReturnQuery()
      );
      return new RedirectResponse($signin_url->toString());
    }
  }
}
