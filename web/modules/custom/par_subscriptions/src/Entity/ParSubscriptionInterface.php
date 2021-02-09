<?php

namespace Drupal\par_subscriptions\Entity;

/**
 * The interface for all Flow Entities.
 */
interface ParSubscriptionInterface {

  /**
   * Get the subscription code.
   *
   * @return string
   */
  public function getCode();

  /**
   * Get the obfuscated email address.
   *
   * @return string
   */
  public function getEmail();

  /**
   * Get the obfuscated email address for display.
   *
   * @return string
   */
  public function displayEmail();

  /**
   * Get the list.
   *
   * @return string
   */
  public function getList();

  /**
   * Is the subscription verified.
   *
   * @return boolean
   */
  public function isVerified();

  /**
   * Subscribe
   *
   * @return string
   */
  public function subscribe();

  /**
   * Verify
   *
   * @return string
   */
  public function verify();

  /**
   * Unsubscribe
   *
   * @return string
   */
  public function unsubscribe();

}
