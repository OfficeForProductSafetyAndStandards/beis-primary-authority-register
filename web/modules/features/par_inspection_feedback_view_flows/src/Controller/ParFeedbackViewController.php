<?php

namespace Drupal\par_inspection_feedback_view_flows\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_inspection_feedback_view_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Symfony\Component\Routing\Route;

/**
 * A controller for rendering a specific partner page.
 */
class ParFeedbackViewController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Inspection plan feedback';

  public function loadData() {
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');

    if ($par_data_inspection_feedback && $par_data_partnership = $par_data_inspection_feedback->getPartnership(TRUE)) {
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    }

    if ($par_data_inspection_feedback && $replies = $par_data_inspection_feedback->getReplies()) {
      $this->getFlowDataHandler()->setParameter('comments', $replies);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'message_detail', $replies);
    }

    parent::loadData();
  }

  public function build($build = []) {
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');
    if ($par_data_inspection_feedback) {
      $this->addCacheableDependency($par_data_inspection_feedback);
    }

    return parent::build($build);
  }

}
