<?php

namespace Drupal\par_deviation_review_flows\Form;

use Drupal\par_deviation_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * Reviewing a deviation request.
 */
class ParDeviationResponseForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Respond to request";

  public function loadData() {
    $par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request');

    if ($par_data_deviation_request && $par_data_partnership = $par_data_deviation_request->getPartnership(TRUE)) {
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    }

    parent::loadData();
  }
}
