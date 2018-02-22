<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * The member contact form.
 */
class ParContactForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'Add member contact details';

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_organisation->getPerson(TRUE));
    parent::loadData();
  }

}
