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

    // Generate the link for confirming partnership details.
    $overview_link = $this->getFlow()->getLinkByStep(4)
      ->setText('Review and confirm your partnership details')
      ->toString();

    // Generate the link for inviting users.
    $organisation_people = $par_data_partnership->getOrganisationPeople();
    if ($organisation_primary_person = array_shift($organisation_people)) {
      $invite_link = $this->getFlow()->getLinkByStep(7, [
        'par_data_person' => $organisation_primary_person->id()
      ])
        ->setText('Invite the business to confirm their details')
        ->toString();
    }

    // Generate the link for confirming inspection plans.
    $inspection_plan_link = $this->getFlow()->getLinkByStep(8)
      ->setText('Review and confirm your inspection plan')
      ->toString();
    $par_data_inspection_plan = current($par_data_partnership->get('inspection_plan')->referencedEntities());
    $inspection_plan_completion = $par_data_inspection_plan->getCompletionPercentage();

    // Generate the link for confirming all advice documents.
    $documents_list_link = $this->getFlow()->getLinkByStep(9)
      ->setText($this->t('Review and confirm your documentation for %business', ['%business' => $par_data_organisation->get('organisation_name')->getString()]))
      ->toString();
    // Calculate the average completion of all documentation.
    $document_completion = [];
    foreach ($par_data_partnership->get('advice')->referencedEntities() as $document) {
      $document_completion[] = $document->getCompletionPercentage();
    }
    $documentation_completion = !empty($document_completion) ? $this->parDataManager->calculateAverage($document_completion) : 0;

    // Build the task list able rows.
    $rows = [
      0 => [
        $overview_link,
        $par_data_partnership->getParStatus(),
      ],
      2 => [
        $inspection_plan_link,
        $inspection_plan_completion . '%',
      ],
      3 => [
        $documents_list_link,
        $documentation_completion . '%',
      ]
    ];
    if (isset($invite_link)) {
      $rows[1] = [
        $invite_link,
        '',
      ];
    }
    // Sort the array by descired keys.
    ksort($rows);

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

