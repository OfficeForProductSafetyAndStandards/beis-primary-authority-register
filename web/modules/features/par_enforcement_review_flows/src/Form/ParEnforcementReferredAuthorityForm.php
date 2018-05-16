<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementReferredAuthorityForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Respond to notice of enforcement actions | Refer enforcement action(s)";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    if ($par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice')) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_notice->getEnforcementActions());
    }

    parent::loadData();
  }

}
