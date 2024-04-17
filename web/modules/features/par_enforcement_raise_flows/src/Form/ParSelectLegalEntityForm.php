<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The member contact form.
 */
class ParSelectLegalEntityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Enforce legal entity';

  /**
   * Load the data for this.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $member_select_cid = $this->getFlowNegotiator()->getFormKey('member_selection');
    $organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', NULL, $member_select_cid);

    // Load the organisation to select legal entities for.
    if ($par_data_partnership && $par_data_partnership->isDirect() &&
      $par_data_organisation = $par_data_partnership->getOrganisation(TRUE)) {
      // Set the organisation.
      $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    }
    elseif ($par_data_partnership && $par_data_partnership->isCoordinated() && $organisation_id &&
      $par_data_organisation = ParDataOrganisation::load($organisation_id)) {
      // Set the organisation.
      $this->getFlowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);
    }

    parent::loadData();
  }

}
