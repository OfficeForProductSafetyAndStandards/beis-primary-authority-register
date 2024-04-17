<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormPluginInterface;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = "Respond to notice of enforcement actions | Proposed enforcement action(s)";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);

      // In order to display multiple cardinality the enforcement_action_review
      // plugin needs to know how many instances of data to display, it doesn't
      // use this data other than to know how many instances of data to display.
      // The actual displayed data comes from the par_data_enforcement_actions
      // parameter set above.
      $action_detail_component = $this->getComponent('enforcement_action_review');
      if ($action_detail_component instanceof ParFormPluginInterface) {
        $values = [];
        foreach ($par_data_enforcement_actions as $action) {
          $values[] = ['action_title' => $action->label()];
        }
        $this->getFlowDataHandler()->setPluginTempData($action_detail_component, $values);
      }
    }

    parent::loadData();
  }

}
