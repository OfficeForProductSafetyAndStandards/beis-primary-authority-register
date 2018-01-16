<?php

namespace Drupal\par_flows;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\par_data\ParDataManagerInterface;
use Psr\Log\LoggerAwareTrait;

class ParFlowNegotiator implements ParFlowNegotiatorInterface {

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * The current route matcher.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * The flow storage.
   *
   * @var \Drupal\par_flows\ParFlowStorage
   */
  protected $flow_storage;

  /**
   * Constructs a ParDataPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route
   *   The entity bundle info service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ParDataManagerInterface $par_data_manager, CurrentRouteMatch $current_route) {
    $this->entityTypeManager = $entity_type_manager;
    $this->parDataManager = $par_data_manager;
    $this->route = $current_route;

    $this->flow_storage = $entity_type_manager->getStorage('par_flow');
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
