<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_search_partnership_flows\ParFlowAccessTrait;

/**
 * A controller for rendering a list of advice documents.
 */
class ParPartnershipFlowsAdviceListController extends ParBaseController {

  use ParFlowAccessTrait;

  protected $pageTitle = 'Advice';

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

    $advice_search_block_exposed  = views_embed_view('advice_lists', 'advice_search_block_exposed', $par_data_partnership_id);
    $build['advice_search_block'] = $advice_search_block_exposed;

    // When new advice is added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    // Make sure changes to the partnership invalidate this page.
    if ($par_data_partnership) {
      $this->addCacheableDependency($par_data_partnership);
    }

    return parent::build($build);
  }

}
