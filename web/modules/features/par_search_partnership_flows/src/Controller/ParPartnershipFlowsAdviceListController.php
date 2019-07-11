<?php

namespace Drupal\par_search_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use \Drupal\views\Views;

/**
 * A controller for rendering a list of advice documents.
 */
class ParPartnershipFlowsAdviceListController extends ParBaseController {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Advice';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    $par_data_partnership_id = !empty($par_data_partnership) ? $par_data_partnership->id() : NULL;

    $build['partnership'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $build['partnership']['title'] = [
      '#type' => 'markup',
      '#markup' => $par_data_partnership->label(),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    $advice_search_block_exposed  = views_embed_view('partnership_search', 'advice_search_block_exposed', $par_data_partnership_id);
    $build['advice_search_block'] = $advice_search_block_exposed;

    // When new advice is added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    // Make sure changes to the partnership invalidate this page
    if ($par_data_partnership) {
      $this->addCacheableDependency($par_data_partnership);
    }

    return parent::build($build);
  }
}
