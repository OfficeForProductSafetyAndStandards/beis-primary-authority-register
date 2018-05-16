<?php

namespace Drupal\par_enforcement_review_flows\Controller;

use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementConfirmController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Response to notification of enforcement action sent';

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    if ($par_data_authority = $par_data_enforcement_notice->getPrimaryAuthority(TRUE)) {
      // Display the help contact fo this partnership.
      $build['help_text'] = $this->renderSection('If you have any questions you can contact', $par_data_authority, ['field_person' => 'summary'], [], TRUE, TRUE);
    }

    $build = parent::build($build);

    return $build;

  }
}
