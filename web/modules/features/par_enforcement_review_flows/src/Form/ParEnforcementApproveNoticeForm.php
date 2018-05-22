<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {
  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Respond to notice of enforcement actions | Proposed enforcement action(s)";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $par_data_enforcement_actions);
    }

    parent::loadData();
  }
}
