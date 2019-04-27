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

    $advice_search_block_exposed  = views_embed_view('partnership_search', 'advice_search_block_exposed', $par_data_partnership_id);
    $build['advice_search_block'] = $advice_search_block_exposed;

    // Check permissions before adding the links for all operations.
    if ($this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      $build['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
      ];

      // PAR-1359 only allow advice uploading on active partnerships as only active partnerships have regulatory
      // functions assigned to them.
      if (!$par_data_partnership->inProgress()) {
        $build['actions']['upload'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlowNegotiator()->getFlow()->getNextLink('upload', $this->getRouteParams())
                ->setText('Upload advice')
                ->toString(),
          ]),
        ];
      }
    }
    return parent::build($build);
  }
}
