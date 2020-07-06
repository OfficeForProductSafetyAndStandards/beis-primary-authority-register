<?php

namespace Drupal\unique_pager;

use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Session\AccountInterface;

class UniquePagerService {

  /**
   * The starting ID.
   *
   * Avoids overlapping with default system pagers.
   */
  const PAGER_START = 3;

  /**
   * The pagers for the current page.
   *
   * Keyed by pager element, with the pager id as the value.
   * @example [3 => 'first_item_pager']
   */
  protected $pagers = [];

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The pager manager service.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * Constructs a ParFlowDataHandler instance.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user account.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pager_manager
   *   The pager manager service.
   */
  public function __construct(AccountInterface $current_user, PagerManagerInterface $pager_manager) {
    $this->account = $current_user;
    $this->pagerManager = $pager_manager;
  }

  /**
   * Get's the current user account.
   *
   * @return \Drupal\Core\Session\AccountInterface|null
   */
  public function getCurrentUser() {
    return $this->account;
  }

  /**
   * Get's the pager manager service.
   *
   * @return \Drupal\Core\Pager\PagerManagerInterface
   */
  public function getPagerManager() {
    return $this->pagerManager;
  }

  /**
   * Get the pager element based on a unique id for the pager.
   *
   * @param string $id
   *   The pager id that we want to retrieve.
   *
   * @return int
   *   The pager element for the given id.
   */
  public function getPager($id) {
    $element = array_search($id, $this->pagers, TRUE);

    if ($element === FALSE) {
      // Get the next pager number.
      $element = $this->getNextAvailablePager();
      $this->setPager($id, $element);
    }

    return $element;
  }

  /**
   * Identify what the next available pager element is.
   *
   * @return int
   *   The next available pager element.
   */
  public function getNextAvailablePager() {
    if (empty($this->pagers)) {
      return self::PAGER_START;
    }
    else {
      $last_element = max(array_keys($this->pagers));
      return ++$last_element;
    }
  }

  /**
   * Get the pager element based on the id.
   *
   * @param string $id
   *   The pager id that we want to retrieve.
   */
  public function setPager($id, $element) {
    if (!isset($this->pagers[$element])) {
      $this->pagers[(int) $element] = $id;
    }
  }
}
