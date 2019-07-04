<?php

namespace Drupal\par_partnership_flows\Controller;

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
    $upload_new_advice = TRUE;

    switch ($this->getFlowNegotiator()->getFlowName()) {
      case 'partnership_authority':
      case 'partnership_direct':
      case 'partnership_coordinated':
        $advice_listing_view_block = 'advice_list_block_exposed';
        break;
      case 'search_partnership':
        $advice_listing_view_block = 'advice_search_block_exposed';
        $upload_new_advice = FALSE;
        break;
    }

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

    $advice_search_block_exposed  = views_embed_view('partnership_search', $advice_listing_view_block, $par_data_partnership_id);
    $build['advice_search_block'] = $advice_search_block_exposed;

    // PAR-1359 only allow advice uploading on active partnerships as only active partnerships have regulatory
    // functions assigned to them. Hide upload button when user is on the search path.
    if ($par_data_partnership->isActive() && $upload_new_advice) {
      $build['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group', 'btn-link-upload']],
      ];

      $build['actions']['upload'] = [
        '#type' => 'markup',
        '#markup' => '<br>' . t('@link', [
          '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('edit', $this->getRouteParams())
              ->setText('Upload advice')
              ->toString(),
        ]),
      ];
    }

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
