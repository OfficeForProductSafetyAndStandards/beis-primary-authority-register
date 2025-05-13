<?php

namespace Drupal\par_enforcement_send_flows\Controller;

use Drupal\par_enforcement_send_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_forms\ParFormPluginInterface;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementSendController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Title callback default.
   */
  #[\Override]
  public function titleCallback() {
    if ($par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice')) {
      $enforcing_authority = $par_data_enforcement_notice->getEnforcingAuthority(TRUE);
    }

    $this->pageTitle = isset($enforcing_authority) ? $enforcing_authority->label() : 'Unknown authority';

    return parent::titleCallback();
  }

  /**
 *
 */
  #[\Override]
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);

      // In order to display multiple cardinality the enforcement_action_detail
      // plugin needs to know how many instances of data to display, it doesn't
      // use this data other than to know how many instances of data to display.
      // The actual displayed data comes from the par_data_enforcement_actions
      // parameter set above.
      $action_detail_component = $this->getComponent('enforcement_action_detail');
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

  /**
 *
 */
  #[\Override]
  public function build($build = []) {
    // Change the action to done.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
