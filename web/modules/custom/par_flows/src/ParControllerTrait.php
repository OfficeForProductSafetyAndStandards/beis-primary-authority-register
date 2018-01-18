<?php

namespace Drupal\par_flows;

use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProvider;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

trait ParControllerTrait {

  /**
   * Default page title.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  protected $defaultTitle = 'Primary Authority Register';

  /**
   * The account for the current logged in user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $currentUser;

  /**
   * The flow negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  protected $negotiator;

  /**
   * The flow data manager.
   *
   * @var \Drupal\par_flows\ParFlowDataHandlerInterface
   */
  protected $flowDataHandler;

  /**
   * The PAR Data Manager.
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
   * {@inheritdoc}
   */
  public function getFlowNegotiator() {
    return $this->negotiator;
  }

  /**
   * {@inheritdoc}
   */
  public function getflowDataHandler() {
    return $this->flowDataHandler;
  }

  /**
   * {@inheritdoc}
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * Returns the default title.
   */
  public function getDefaultTitle() {
    return $this->defaultTitle;
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    return $this->getDefaultTitle();
  }

}
