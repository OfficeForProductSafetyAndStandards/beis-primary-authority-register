<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Make a decision | Proposed enforcement action(s)";

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
