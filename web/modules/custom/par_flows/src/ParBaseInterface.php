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
  public function getFlowDataHandler();

  /**
   * Returns the PAR data manager.
   *
   * @return string
   *   Get the logger channel to use.
   */
  public function getParDataManager();

}
