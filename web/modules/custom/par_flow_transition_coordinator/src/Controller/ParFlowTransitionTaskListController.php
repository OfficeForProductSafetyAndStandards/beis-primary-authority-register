<?php

namespace Drupal\par_flow_transition_coordinator\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Controller\ParFlowTransitionTaskListController as ParFlowTransitionTaskListBusinessController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionTaskListController extends ParFlowTransitionTaskListBusinessController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, $key_text = NULL) {
    $build = parent::content($par_data_partnership, 'association');

    return $build;
  }

}
