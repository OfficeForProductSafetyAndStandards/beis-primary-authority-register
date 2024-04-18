<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParFlowAccessTrait;

/**
 * Confirmation form.
 *
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Confirm the primary contact details';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_person = $partnership ? $partnership->getOrganisationPeople(TRUE) : NULL;

    // Override the route parameter to get data loaded will be from this entity.
    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);

    parent::loadData();
  }

}
