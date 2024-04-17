<?php

namespace Drupal\par_inspection_feedback_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_inspection_feedback_flows\ParFlowAccessTrait;

/**
 * A controller for rendering a specific partner page.
 */
class ParFeedbackConfirmationForm extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Inspection plan feedback sent';

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL) {
    // Display the help contact fo this partnership.
    $build['help_text'] = $this->renderSection('If you have any questions you can contact', $par_data_partnership, ['field_authority_person' => 'summary'], [], TRUE, TRUE);

    // In order to redirect to a page outside this flow.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
