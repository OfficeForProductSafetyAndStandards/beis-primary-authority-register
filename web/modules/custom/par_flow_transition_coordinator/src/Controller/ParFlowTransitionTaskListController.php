<?php

namespace Drupal\par_flow_transition_coordinator\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Controller\ParFlowTransitionTaskListController as ParFlowTransitionTaskListBusinessController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionTaskListController extends ParFlowTransitionTaskListBusinessController {

  /**
   * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
   * to the end of the array.
   *
   * @param array $array
   * @param string $key
   * @param array $new
   *
   * @return array
   */
  private function array_insert_after( array $array, $key, array $new ) {
    $keys = array_keys( $array );
    $index = array_search( $key, $keys );
    $pos = false === $index ? count( $array ) : $index + 1;
    return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
  }

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, $key_text = NULL) {
    $build = parent::content($par_data_partnership, 'association');

    $build_notice['notice'] = [
      '#markup' => t('After 01 October 2017 you have a statutory duty to provide an up-to-date list of members on request. Get your membership list ready for this date.'),
      '#prefix' => '<p>',
      '#sufffix' => '</p>',
    ];

    $build = $this->array_insert_after($build, 'intro', $build_notice);

    return $build;
  }

}
