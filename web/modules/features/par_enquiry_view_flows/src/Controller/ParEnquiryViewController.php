<?php

namespace Drupal\par_enquiry_view_flows\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_enquiry_view_flows\ParFlowAccessTrait;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginInterface;
use Symfony\Component\Routing\Route;

/**
 * A controller for rendering a specific partner page.
 */
class ParEnquiryViewController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'View enquiry';

  #[\Override]
  public function loadData() {
    $par_data_general_enquiry = $this->getFlowDataHandler()->getParameter('par_data_general_enquiry');

    if ($par_data_general_enquiry && $par_data_partnership = $par_data_general_enquiry->getPartnership(TRUE)) {
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    }

    if ($par_data_general_enquiry && $replies = $par_data_general_enquiry->getReplies()) {
      $this->getFlowDataHandler()->setParameter('comments', $replies);

      // In order to display multiple cardinality the message_detail plugin needs
      // to know how many instances of data to display, it doesn't use this data
      // other than to know how many instances of data to display. The actual
      // displayed data comes from the comments parameter set above.
      $action_detail_component = $this->getComponent('message_detail');
      if ($action_detail_component instanceof ParFormPluginInterface) {
        $values = [];
        foreach ($replies as $reply) {
          $values[] = ['comment_title' => $reply->label()];
        }
        $this->getFlowDataHandler()->setPluginTempData($action_detail_component, $values);
      }
    }

    parent::loadData();
  }

  #[\Override]
  public function build($build = []) {
    $par_data_general_enquiry = $this->getFlowDataHandler()->getParameter('par_data_general_enquiry');
    if ($par_data_general_enquiry) {
      $this->addCacheableDependency($par_data_general_enquiry);
    }

    // Change the action to done.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
