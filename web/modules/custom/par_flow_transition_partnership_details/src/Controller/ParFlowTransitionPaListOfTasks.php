<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionPaListOfTasks extends ParBaseController {

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
    $organisation_view_builder = $par_data_organisation->getViewBuilder();

    // Premises.
    $par_data_premises = current($par_data_organisation->get('premises')->referencedEntities());
    $premises_view_builder = $par_data_premises->getViewBuilder();

    $build['premises'] = $premises_view_builder->view($par_data_premises, 'summary');

    // Primary contact summary.
    $par_data_primary_person = current($par_data_partnership->get('person')->referencedEntities());
    $primary_person_view_builder = $par_data_primary_person->getViewBuilder();

    $build['primary_contact'] = $primary_person_view_builder->view($par_data_primary_person, 'summary');

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
      '#theme' => 'par_some_custom_theme',
      '#header' => $header,
      'title' => $rows,
      'name' => $this->t("No tasks could be found."),
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

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}

