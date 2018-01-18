<?php

namespace Drupal\par_flows;

/**
 * The interface for all Par Base Controllers.
 */
interface ParBaseInterface {

  /**
   * Returns the logger channel specific to errors logged by PAR Forms.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getLoggerChannel();

  /**
   * Returns the PAR data manager.
   *
   * @return \Drupal\par_flows\ParFlowNegotiatorInterface
   *   Get the flow negotiator.
   */
  public function getFlowNegotiator();

  /**
   * Returns the PAR data manager.
   *
   * @return \Drupal\par_flows\ParFlowDataHandlerInterface
   *   Get the flow data handler.
   */
  public function getflowDataHandler()v;

  /**
   * Returns the PAR data manager.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getParDataManager();

  /**
   * Get the current flow name.
   *
   * @return string
   *   The string representing the name of the current flow.
   */
  public function getFlowName();

  /**
   * Get the current flow entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The flow entity.
   */
  public function getFlow();

  /**
   * Get the injected Flow Entity Storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The flow storage handler.
   */
  public function getFlowStorage();

}
