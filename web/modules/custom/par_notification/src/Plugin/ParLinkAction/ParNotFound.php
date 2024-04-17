<?php

namespace Drupal\par_notification\Plugin\ParLinkAction;

use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_notification\ParLinkActionBase;

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

  /**
   * {@inheritDoc}
   */
  public function getUrl(MessageInterface $message): ?Url {
    // This should be the last redirection to happen if no other one could be found.
    return Url::fromRoute('par_notification.link_not_found', ['message' => $message->id()]);
  }

}
