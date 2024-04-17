<?php

namespace Drupal\par_inspection_feedback_view_flows\Controller;

use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_forms\ParFormPluginInterface;
use Drupal\par_inspection_feedback_view_flows\ParFlowAccessTrait;

/**
 * A controller for rendering a specific partner page.
 */
class ParFeedbackViewController extends ParBaseController {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Inspection plan feedback';

  /**
   * Load the data for this.
   */
  public function loadData() {
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');

    if ($par_data_inspection_feedback && $par_data_partnership = $par_data_inspection_feedback->getPartnership(TRUE)) {
      $this->getFlowDataHandler()->setParameter('par_data_partnership', $par_data_partnership);
    }

    if ($par_data_inspection_feedback && $replies = $par_data_inspection_feedback->getReplies()) {
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

  /**
   *
   */
  public function build($build = []) {
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');
    if ($par_data_inspection_feedback) {
      $this->addCacheableDependency($par_data_inspection_feedback);
    }

    // Change the action to done.
    $this->getFlowNegotiator()->getFlow()->setActions(['done']);

    return parent::build($build);
  }

}
