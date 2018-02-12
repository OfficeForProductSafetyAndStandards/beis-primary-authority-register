<?php

namespace Drupal\par_member_add_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_add_flows\ParFlowAccessTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Confirm the primary contact details';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_contact';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_person = $partnership ? $partnership->getOrganisationPeople(TRUE) : NULL;

    // Override the route parameter so that data loaded will be from this entity.
    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);

    parent::loadData();
  }

}
