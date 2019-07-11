<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\ParFlowException;
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

    switch ($this->getFlowNegotiator()->getFlowName()) {
      case 'partnership_authority':
        $advice_list_block = 'advice_list_authority_block';

        break;

      case 'partnership_direct':
      case 'partnership_coordinated':
        $advice_list_block = 'advice_list_organisation_block';

        break;

    }
    if ($advice_list_block) {
      $advice_search_block_exposed = views_embed_view('advice_lists', $advice_list_block, $par_data_partnership_id);
      $build['advice_search_block'] = $advice_search_block_exposed;
    }
    else {
      $build['advice_search_block'] = [
        '#type' => 'markup',
        '#markup' => "Advice can't be listed here. Please contact the helpdesk.",
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }

    // PAR-1359 only allow advice uploading on active partnerships as only active partnerships have regulatory
    // functions assigned to them. Hide upload button when user is on the search path.
    if ($par_data_partnership->isActive() && $this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      $build['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['form-group', 'btn-link-upload']],
      ];

      try {
        $build['actions']['upload'] = [
          '#type' => 'markup',
          '#markup' => '<br>' . t('@link', [
              '@link' => $this->getFlowNegotiator()
                ->getFlow()
                ->getNextLink('upload', $this->getRouteParams())
                ->setText('Upload advice')
                ->toString(),
            ]),
        ];
      }
      catch (ParFlowException $e) {

      }
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
