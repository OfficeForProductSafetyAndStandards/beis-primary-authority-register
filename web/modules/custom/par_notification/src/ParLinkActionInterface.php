<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\message\MessageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines an interface for the Par Link Action plugins.
 *
 * @see plugin_api
 */
interface ParLinkActionInterface extends PluginInspectionInterface {

  /**
   * Processes a received request from a notification link.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message interface that needs redirection.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   Returns a redirection response if there is an available action.
   */
  public function receive(MessageInterface $message): ?RedirectResponse;

  /**
   * Gets the next available notification link action.
   *
   * @param \Drupal\message\Entity\Message $message
   *   The message interface that needs redirection.
   *
   * @return \Drupal\Core\Link|null
   *   Returns a link if there is an available action.
   */
  public function getLink(MessageInterface $message): ?Link;

  /**
   * Set the user that is being redirected.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user account that is requesting redirection.
   */
  public function setUser(AccountInterface $user);

  /**
   * Set the query parameter to be appended to URLs that are required to be processed sequentially.
   *
   * @param array $query
   *   A query array that can be added to any array that needs to redirect back to the link manager.
   */
  public function setReturnQuery($query);

}
