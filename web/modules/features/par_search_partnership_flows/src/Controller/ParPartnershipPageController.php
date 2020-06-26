<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\ParFlowException;

/**
 * A controller for rendering a specific partner page.
 */
class ParPartnershipPageController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'search_partnership';

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    if ($par_data_partnership) {
      $par_data_organisation = current($par_data_partnership->getOrganisation());

      if ($par_data_organisation && $org_name = $par_data_organisation->get('organisation_name')->getString()) {
        $this->pageTitle = "Primary authority information for | {$org_name}";
      }
    }
    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function build($build = [], ParDataPartnership $par_data_partnership = NULL) {
    // Make sure changes to the partnership invalidate this page
    if ($par_data_partnership) {
      $this->addCacheableDependency($par_data_partnership);
    }

    return parent::build($build);

  }
}
