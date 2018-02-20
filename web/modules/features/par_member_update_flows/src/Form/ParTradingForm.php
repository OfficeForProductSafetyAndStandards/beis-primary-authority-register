<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * Add trading names.
 */
class ParTradingForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Add trading name";

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business');
    $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
    $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    parent::loadData();
  }

}
