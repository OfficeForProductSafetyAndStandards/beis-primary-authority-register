<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionPaListOfTasks extends ParBaseController  {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->get('organisation')->referencedEntities());
    $form['organisation_summary'] = $par_data_organisation->getViewBuilder()->view($par_data_organisation, 'teaser');

    // Primary contact summary
    $par_data_people = $par_data_partnership->get('person')->referencedEntities();
    $par_data_primary_person = array_shift($par_data_people);
    $build['primary_contact'] = $par_data_primary_person->getViewBuilder()->view($par_data_primary_person, 'teaser');

    // Table headers.
    $header = [];

    // Table data/cells.
    $overview_link = $this->getFlow()->getLinkByStep(3)
      ->setText('Review and confirm your partnership details')
      ->toString();
    $rows = [
      [
        $overview_link,
        $par_data_partnership->getParStatus(),
      ]
    ];

    // Task List.
    // $form['basic_table_title'] = ['#markup' => '<h2 class="heading-medium">' . $this->t("Basic data table") . '</h2>'];
    $build['basic_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("No tasks could be found."),
    ];

    $build['save_and_continue'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getLinkByRoute('par_flow_transition_partnership_details.overview', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save and continue')
          ->toString()
      ]),
    ];

    $build['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(1)
          ->setText('Cancel')
          ->toString()
      ]),
    ];

    return $build;

  }

}

