<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Final redirection to dashboard.
 *
 * @ParLinkAction(
 *   id = "page_not_found",
 *   title = @Translation("Cannot find the page that should be redirect to."),
 *   status = TRUE,
 *   weight = 99,
 * )
 */
class ParNotFound extends ParLinkActionBase {

  public function receive(MessageInterface $message) {
    // This should be the last redirection to happen if no other one could be found.
    $dashboard_url = Url::fromRoute('par_notification.link_not_found', ['message' => $message->id()])->toString();
    return new RedirectResponse($dashboard_url);
  }
}
