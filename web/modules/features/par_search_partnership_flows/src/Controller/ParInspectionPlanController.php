<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_search_partnership_flows\ParFlowAccessTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParInspectionPlanController extends ParBaseController {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Inspection plans';

  /**
   * {@inheritdoc}
   */
  public function content(?ParDataPartnership $par_data_partnership = NULL) {

    $par_data_partnership_id = !empty($par_data_partnership) ? $par_data_partnership->id() : NULL;

    $build['partnership'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'govuk-form-group'],
    ];
    $build['partnership']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $par_data_partnership->label(),
      '#attributes' => ['class' => 'govuk-heading-m'],
    ];

    $inspection_plan_search_block_exposed  = views_embed_view('inspection_plan_lists', 'inspection_plan_search_block', $par_data_partnership_id);
    $build['inspection_plan_search_block'] = $inspection_plan_search_block_exposed;

    // When a new inspection plan is added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    // Make sure changes to the partnership invalidate this page.
    if ($par_data_partnership) {
      $this->addCacheableDependency($par_data_partnership);
    }

    return parent::build($build);
  }

}
