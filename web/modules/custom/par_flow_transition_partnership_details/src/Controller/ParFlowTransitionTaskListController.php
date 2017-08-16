<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for displaying all tasks that can
 * be performed on a partnership.
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
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_name = $par_data_organisation->get('name')->getString();

    // Premises.
    $par_data_premises = current($par_data_organisation->getPremises());
    $premises_view_builder = $par_data_premises->getViewBuilder();

    // Organisation Name & Address.
    $build['organisation']['label'] = [
      '#type' => 'markup',
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
      '#markup' => $organisation_name
    ];

    $build['organisation']['premises_address'] = $premises_view_builder->view($par_data_premises, 'summary');

    // Primary contact summary.
    $par_data_primary_person = current($par_data_partnership->getAuthorityPeople());
    $primary_person_view_builder = $par_data_primary_person->getViewBuilder();

    $build['primary_contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
    ];

    $build['primary_contact']['label'] = [
      '#type' => 'markup',
      '#prefix' => '<h4>',
      '#suffix' => '</h4>',
      '#markup' => 'Main contact:'
    ];
    $build['primary_contact']['person'] = $primary_person_view_builder->view($par_data_primary_person, 'summary');

    // Generate the link for confirming partnership details.
    $overview_link = $this->getFlow()->getLinkByStep(4)
      ->setText('Review and confirm your partnership details')
      ->toString();

    // Generate the link for inviting users.
    $organisation_people = $par_data_partnership->getOrganisationPeople();
    if ($organisation_primary_person = array_shift($organisation_people)) {
      $invite_link = $this->getFlow()->getLinkByStep(7, [
        'par_data_person' => $organisation_primary_person->id(),
      ])
        ->setText('Invite the business to confirm their details')
        ->toString();
    }

    // Generate the link for confirming inspection plans.
    $inspection_plan_link = $this->getFlow()->getLinkByStep(8)
      ->setText('Review and confirm your inspection plan')
      ->toString();
    $par_data_inspection_plan = current($par_data_partnership->getInspectionPlan());
    $inspection_plan_status = $par_data_inspection_plan->getParStatus();

    // Make sure to add the inspection plan cacheability data to this form.
    $this->addCacheableDependency($par_data_inspection_plan);

    // Generate the link for confirming all advice documents.
    $documents_list_link = $this->getFlow()->getLinkByStep(9)
      ->setText($this->t('Review and confirm your documentation for @business', ['@business' => $par_data_organisation->get('organisation_name')->getString()]))
      ->toString();
    // Calculate the average completion of all documentation.
    $document_completion = [];
    foreach ($par_data_partnership->getAdvice() as $document) {
      $document_completion[] = $document->getCompletionPercentage();

      // Make sure to add the document cacheability data to this form.
      $this->addCacheableDependency($document);
    }
    $documentation_completion = !empty($document_completion) ? $this->parDataManager->calculateAverage($document_completion) : 0;

    // Build the task list able rows.
    $rows = [
      0 => [
        $overview_link,
        $par_data_partnership->getParStatus(),
      ]
    ];

    // Only add the remaining tasks once the Partnership information has been confirmed.
    if ($par_data_partnership->getRawStatus() !== 'awaiting_review') {
      $rows[2] = [
        $inspection_plan_link,
        $inspection_plan_status,
      ];
      $rows[3] = [
        $documents_list_link,
        $documentation_completion . '%',
      ];
      if (isset($invite_link)) {
        $rows[1] = [
          $invite_link,
          '',
        ];
      }
    }
    // Sort the array by descired keys.
    ksort($rows);

    // Show the task links in table format.
    $build['task_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#header' => [],
      '#rows' => $rows,
      '#empty' => $this->t("No tasks could be found."),
    ];

    $build['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getLinkByStep(1, $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Go back to your partnerships')
          ->toString(),
      ]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}
