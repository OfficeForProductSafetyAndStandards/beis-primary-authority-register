<?php

namespace Drupal\par_notification;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\ParDataManagerInterface;
use RapidWeb\UkBankHolidays\Factories\UkBankHolidayFactory;

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
  protected $user;

  /**
   * The return query used if an action is sequential,
   * as in it is not the final action.
   */
  protected $returnQuery;

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
   * Simple getter to access the current user.
   *
   * @return AccountInterface
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function setUser(AccountInterface $user) {
    $this->user = $user;
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
