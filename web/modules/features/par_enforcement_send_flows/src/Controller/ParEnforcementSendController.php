<?php

namespace Drupal\par_enforcement_send_flows\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_enforcement_send_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Symfony\Component\Routing\Route;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnforcementSendController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * Title callback default.
   */
  public function titleCallback() {
    if ($par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice')) {
      $enforcing_authority = $par_data_enforcement_notice->getEnforcingAuthority(TRUE);
    }

    $this->pageTitle = isset($enforcing_authority) ? $enforcing_authority->label() : 'Unknown authority';

    return parent::titleCallback();
  }

  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_detail', $par_data_enforcement_actions);
    }

    parent::loadData();
  }

}
