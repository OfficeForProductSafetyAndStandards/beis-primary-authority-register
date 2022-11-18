<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\message\MessageInterface;
use Drupal\par_data\ParDataManagerInterface;
use RapidWeb\UkBankHolidays\Factories\UkBankHolidayFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a base implementation for a Par Link Action plugin.
 *
 * @see \Drupal\par_notification\ParLinkActionInterface
 * @see \Drupal\par_notification\ParLinkManager
 * @see \Drupal\par_notification\Annotation\ParLinkAction
 * @see plugin_api
 */
abstract class ParLinkActionBase extends PluginBase implements ParLinkActionInterface {

  /**
   * The account object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $user;

  /**
   * The link text for link action plugin.
   */
  protected string $actionText = 'View the notification';

  /**
   * The return query used if an action is sequential,
   * as in it is not the final action.
   */
  protected array $returnQuery;

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->pluginDefinition['weight'];
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->pluginDefinition['status'];
  }

  /**
   * {@inheritdoc}
   */
  public function getNotifications() {
    return $this->pluginDefinition['notification'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDestination() {
    return $this->pluginDefinition['destination'];
  }

  /**
   * {@inheritdoc}
   */
  public function getAction() {
    return $this->pluginDefinition['action'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPrimaryField(): string {
    return $this->pluginDefinition['field'];
  }

  /**
   * Simple getter to access the current user.
   *
   * @return AccountInterface
   */
  public function getUser(): AccountInterface {
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function setUser(AccountInterface $user) {
    $this->user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public function receive(MessageInterface $message): ?RedirectResponse {
    $destination = $this->getUrl($message);

    return $destination instanceof Url
      && $destination->access($this->user) ?
        new RedirectResponse($destination->toString()) :
        NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLink(MessageInterface $message): ?Link {
    $destination = $this->getUrl($message);

    return $destination instanceof Url
      && $destination->access($this->user)
      && !empty($this->actionText) ?
        Link::fromTextAndUrl($this->actionText, $destination) :
        NULL;
  }

  /**
   * Simple getter to return the return query.
   *
   * @return array
   */
  public function getReturnQuery() {
    return $this->returnQuery;
  }

  /**
   * {@inheritdoc}
   */
  public function setReturnQuery($query) {
    $this->returnQuery = $query;
  }

}
