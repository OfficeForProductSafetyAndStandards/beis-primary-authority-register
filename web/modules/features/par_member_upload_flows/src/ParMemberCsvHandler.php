<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Serializer\Serializer;

class ParMemberCsvHandler implements ParMemberCsvHandlerInterace {

  /**
   * The symfony serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $seriailzer;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a ParFlowNegotiator instance.
   *
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route
   *   The entity bundle info service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The entity bundle info service.
   */
  public function __construct(Serializer $serializer, ParDataManagerInterface $par_data_manager) {
    $this->entityTypeManager = $serializer;
    $this->parDataManager = $par_data_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {

  }

  /**
   * {@inheritdoc}
   */
  public function getColumnsByIndex(int $index) {

  }

  /**
   * {@inheritdoc}
   */
  public function lock() {

  }

  /**
   * {@inheritdoc}
   */
  public function unlock() {

  }

  /**
   * {@inheritdoc}
   */
  public function validateRow() {

  }

  /**
   * {@inheritdoc}
   */
  public function process() {

  }

  /**
   * {@inheritdoc}
   */
  public function cleanup() {

  }

  /**
   * {@inheritdoc}
   */
  public function complete() {
    
  }
}
