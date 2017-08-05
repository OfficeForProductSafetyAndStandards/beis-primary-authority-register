<?php

namespace Drupal\par_flow_transition_business\Controller;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionTaskListController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL, $termporary_no_crashy_variable = NULL) {

    $par_data_authority = current($par_data_partnership->getAuthority());

    $par_data_primary_person = current($par_data_authority->getPerson());
    $primary_person_view_builder = $par_data_primary_person->getViewBuilder();
    $primary_person = $primary_person_view_builder->view($par_data_primary_person, 'summary');


    $build['intro'] = [
      '#markup' => t('Review and confirm the details of your partnership with %primary_authority by 14 September 2017',
        ['%primary_authority' => $par_data_authority->get('authority_name')->getString()]),
    ];

    $build['contact'] = [
      '#type' => 'fieldset',
      '#title' => t('Main contact at the Authority'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $build['contact']['name'] = $this->renderMarkupField($primary_person);

    // Table headers.
    $header = [];

    // Table data/cells.
    $overview_link = $this->getFlow()->getLinkByStep(4)
      ->setText('Review and confirm your business details')
      ->toString();
    $rows = [
      [
        $overview_link,
        $par_data_partnership->getParStatus(),
      ]
    ];

    // Task List.
    $build['basic_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("No tasks could be found."),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}

