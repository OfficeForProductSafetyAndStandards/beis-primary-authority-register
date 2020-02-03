<?php

namespace Drupal\par_reporting;


use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
* Manages all functionality universal to Par Data.
*/
class ParReportingManager implements ParReportingManagerInterface {

  use StringTranslationTrait;

  /**
   * The par data data manager service.
   *
   * @var \Drupal\par_data\ParDataManager
   */
  protected $parDataManager;

  /**
   * The drupal messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a ParReportingManager instance.
   *
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user
   */
  public function __construct(ParDataManagerInterface $par_data_manager, MessengerInterface $messenger, $current_user) {
    $this->parDataManager = $par_data_manager;
    $this->messenger = $messenger;
    $this->currentUser = $current_user;
  }

  /**
   * Dynamic getter for the messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   */
  public function getMessenger() {
    return $this->messenger;
  }

  /**
   * Get current user.
   *
   * @return mixed
   */
  public function getCurrentUser() {
    return $this->currentUser;
  }

}
