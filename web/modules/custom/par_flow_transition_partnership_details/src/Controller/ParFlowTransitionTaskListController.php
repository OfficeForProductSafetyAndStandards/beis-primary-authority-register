<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

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
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->get('organisation')->referencedEntities());

    // Premises.
    $par_data_premises = current($par_data_organisation->get('premises')->referencedEntities());
    $premises_view_builder = $par_data_premises->getViewBuilder();

    $build['premises'] = $premises_view_builder->view($par_data_premises, 'summary');

    // Primary contact summary.
    $par_data_primary_person = current($par_data_partnership->get('person')->referencedEntities());
    $primary_person_view_builder = $par_data_primary_person->getViewBuilder();

    $build['primary_contact'] = $primary_person_view_builder->view($par_data_primary_person, 'summary');

    // Generate the links for each task.
    $overview_link = $this->getFlow()->getLinkByStep(4)
      ->setText('Review and confirm your partnership details')
      ->toString();

    $organisation_people = $par_data_partnership->getOrganisationPeople();
    if ($organisation_primary_person = array_shift($organisation_people)) {
      $invite_link = $this->getFlow()->getLinkByStep(7, [
        'par_data_person' => $organisation_primary_person->id()
      ])
        ->setText('Invite the business to confirm their details')
        ->toString();
    }
    $rows = [
      [
        $overview_link,
        $par_data_partnership->getParStatus(),
      ],
    ];
    if ($invite_link) {
      $rows[] = [
        $invite_link,
        '',
      ];
    }

    // Show the task links in table format.
    $build['task_list'] = [
      '#theme' => 'table',
      '#header' => [],
      '#rows' => $rows,
      '#empty' => $this->t("No tasks could be found."),
    ];

    $build['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(1, $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Go back to your partnerships')
          ->toString()
      ]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}

