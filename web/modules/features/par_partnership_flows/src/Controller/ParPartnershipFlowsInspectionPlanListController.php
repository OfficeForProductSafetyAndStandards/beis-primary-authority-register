<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_flows\ParFlowException;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of inspection plan documents.
 */
class ParPartnershipFlowsInspectionPlanListController extends ParBaseController {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  protected $pageTitle = 'Inspection Plans';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
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

    switch ($this->getFlowNegotiator()->getFlowName()) {
      //View that contains actions i.e. edit, revoke.
      case 'partnership_authority':
        $inspection_plan_list_block = 'inspection_list_authority_block';

        break;
      //none action view.
      case 'partnership_direct':
      case 'partnership_coordinated':
        $inspection_plan_list_block = 'inspection_plan_list_org_block';

        break;
    }

    if ($inspection_plan_list_block) {
      $inspection_plan_list= views_embed_view('inspection_plan_lists', $inspection_plan_list_block, $par_data_partnership_id);
      $build['inspection_plan_list'] = $inspection_plan_list;
    }
    else {
      $build['inspection_plan_list'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => "Inspection plans can't be listed here. Please contact the helpdesk.",
      ];
    }

    // Only allow inspection plan uploading on active partnerships as only active partnerships.
    // Hide upload button when user is on the search path.
    if ($par_data_partnership->isActive() && $this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {
      if ($this->getCurrentUser()->hasPermission('upload partnership inspection plan')) {
      $build['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => ['govuk-form-group']],
      ];

      try {
        $build['actions']['upload'] = [
          '#type' => 'markup',
          '#markup' => '<br>' . t('@link', [
              '@link' => $this->getFlowNegotiator()
                  ->getFlow()
                  ->getNextLink('upload', $this->getRouteParams())
                  ->setText('Upload inspection plan')
                  ->toString(),
            ]),
        ];
      } catch (ParFlowException $e) {

      }
    } else {
        // for none help desk users contact the help-desk text.
        $build['actions'] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('To upload an inspection plan please contact the Help Desk.'),
        ];
      }
    }

    // When new inspection plans are added these can't clear the cache,
    // for now we will keep this page uncached.
    $this->killSwitch->trigger();

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);
  }

}
