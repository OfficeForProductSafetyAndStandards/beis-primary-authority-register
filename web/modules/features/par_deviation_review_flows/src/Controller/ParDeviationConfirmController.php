<?php

namespace Drupal\par_deviation_review_flows\Controller;

use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_deviation_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller to confirm review of a deviation request.
 */
class ParDeviationConfirmController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Response to deviation request sent';

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataDeviationRequest $par_data_deviation_request = NULL) {

    // Information about the next steps.
    $build['next_steps'] = [
      '#title' => $this->t('What happens next?'),
      '#type' => 'fieldset',
    ];
    $build['next_steps']['info'] = [
      '#type' => 'markup',
      '#markup' => "Your response has been sent to the enforcement officer.",
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    if ($par_data_deviation_request) {
      // Display the help contact fo this partnership.
      $build['help_text'] = $this->renderSection('If you have any questions you can contact the enforcement officer', $par_data_deviation_request, ['field_person' => 'summary'], [], TRUE, TRUE);
    }

    $build = parent::build($build);

    return $build;

  }
}
