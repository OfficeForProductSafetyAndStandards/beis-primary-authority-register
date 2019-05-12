<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use \Drupal\views\Views;

/**
 * A controller for rendering a list of advice documents.
 */
class ParPartnershipFlowsAdviceListController extends ParBaseController {

  use ParPartnershipFlowsTrait;

  protected $pageTitle = 'Advice';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    $par_data_partnership_id = !empty($par_data_partnership) ? $par_data_partnership->id() : NULL;

    switch ($this->getFlowNegotiator()->getFlowName()) {
      case 'partnership_authority':
      case 'partnership_direct':
      case 'partnership_coordinated':
        $advice_listing_view_block = 'advice_search_block_exposed';
        break;
      case 'search_partnership':
        $advice_listing_view_block = 'help_desk_advice_search_block';
        break;
    }

    $advice_search_block_exposed  = views_embed_view('partnership_search', $advice_listing_view_block, $par_data_partnership_id);
    $build['advice_search_block'] = $advice_search_block_exposed;

    // If partnership is not active no need to execute anymore logic.
    if ($par_data_partnership->inProgress()) {
      return parent::build($build);
    }

    $build['actions'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['form-group', 'btn-link-upload']],
    ];

    $build['actions']['upload'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('upload', $this->getRouteParams())
            ->setText('Upload advice')
            ->toString(),
      ]),
    ];
    return parent::build($build);
  }
}
