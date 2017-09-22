<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

trait ParControllerTrait {

  /**
   * The account for the current logged in user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $currentUser;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * Get the current user account.
   */
  public function getCurrentUser() {
    return $this->currentUser;
  }

  /**
   * Set the current user account.
   */
  public function setCurrentUser(AccountInterface $account = NULL) {
    if (\Drupal::currentUser()->isAuthenticated() && !$this->getCurrentUser()) {
      $id = $account ? $account->id() : \Drupal::currentUser()->id();
      $this->currentUser = User::load($id);
    }
  }

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel() {
    return 'par';
  }

  /**
   * Returns the PAR data manager.
   *
   * @return \Drupal\par_data\ParDataManagerInterface
   *   Get the logger channel to use.
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    return $this->t('Primary Authority Register');
  }

}
