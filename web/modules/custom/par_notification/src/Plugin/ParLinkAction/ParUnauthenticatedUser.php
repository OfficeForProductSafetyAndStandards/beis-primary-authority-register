<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;

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

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    // If the user is not logged in, allow them to login first.
    if (!$this->user->isAuthenticated()) {
      // Redirect to user login page with the appended destination query.
      return Url::fromRoute(
        'par_notification.link_access_denied',
        ['message' => $message->id()],
        $this->getReturnQuery()
      );
    }

    return NULL;
  }

}
