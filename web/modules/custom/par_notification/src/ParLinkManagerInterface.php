<?php

namespace Drupal\par_notification;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTemplateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines an interface for the PAR Link Manager service.
 *
 * @see plugin_api
 */
interface ParLinkManagerInterface {

  /**
   * Return all link actions that contain tasks.
   *
   * Tasks are actions that require active input and need to be completed.
   *
   * @see PAR-1948 - https://regulatorydelivery.atlassian.net/browse/PAR-1948
   *
   * @param MessageTemplateInterface $message_template
   *   The message template (aka the message bundle entity).
   *
   * @return ParTaskInterface[]
   */
  public function retrieveTasks(MessageTemplateInterface $message_template): array;


    /**
   * Generate the link manager URL.
   *
   * @param integer|string $message_id
   *   Can be a message id or a message replacement token.
   *
   * @return \Drupal\Core\Url
   *   The generated link manager URL.
   */
  public function generateLink(int|string $message_id): Url;

  /**
   * Processes an incoming received message, and redirect to the
   * correct destination.
   *
   * Plugins will handle fallback mechanisms in cases where the user is
   * not signed in, or the page is not accessible.
   *
   * @param RouteMatchInterface $route
   *   The link manager route for this message.
   * @param MessageInterface $message
   *   The notification message that is being actioned.
   *
   * @throws ParNotificationException
   *   When the link cannot be accessed after all the checks have been made.
   *
   * @return RedirectResponse|void
   */
  public function receive(RouteMatchInterface $route, MessageInterface $message): ?RedirectResponse;

  /**
   * Get the appropriate action link.
   *
   * Plugins will handle fallback mechanisms in cases where the user is
   * not signed in, or the page is not accessible.
   *
   * @param MessageInterface $message
   *   The notification message that is being actioned.
   *
   * @throws ParNotificationException
   *   When the link cannot be accessed after all the checks have been made.
   *
   * @return Link|void
   *   The primary link for this notification.
   */
  public function link(MessageInterface $message): ?Link;

}
