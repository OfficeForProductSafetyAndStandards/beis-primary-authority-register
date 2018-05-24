<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParEmployeeNumberForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Confirm number of employees";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;

    // For the apply journey we will always edit the first value.
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);

    parent::loadData();
  }

}
