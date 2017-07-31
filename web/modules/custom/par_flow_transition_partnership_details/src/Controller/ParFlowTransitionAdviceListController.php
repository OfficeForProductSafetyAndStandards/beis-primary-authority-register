<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for all PAR Flow Transition pages.
 */
class ParFlowTransitionAdviceListController extends ParBaseController {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {

    $rows = [];

    // Organisation summary.
    $documents = $par_data_partnership->get('advice')->referencedEntities();

    foreach ($documents as $document) {
      $document_view_builder = $document->getViewBuilder();
      // The first column contains a rendered summary of the document.
      $document_summary = $document_view_builder->view($document, 'summary');

      // The second column contains a summary of the confirmed details.
      $document_details = 'Awaiting confirmation';
      if ($advice_type = $document->get('advice_type')->getString()) {
        $document_details = "{$advice_type}";
        if ($we_create_reference_between_documents_and_regulatory_function = FALSE) {
          $document_details .= "covering:" . PHP_EOL . PHP_EOL;
        }
      }

      // The third column contains a list of actions that can be performed on this document.
      $document_actions = implode(PHP_EOL, [
        $this->getFlow()
          ->getLinkByStep(10, ['par_data_advice' => $document->id()])
          ->setText('edit')
          ->toString(),
      ]);

      // Fourth column contains the completion status of the document.
      $completion = $document->getCompletionPercentage();

      if ($document_summary && $document_details && $document_actions && $completion) {
        $row[] = [
          $document_summary,
          $document_details,
          $document_actions,
          $completion,
        ];
      }
    }

    // Show the task links in table format.
    $build['task_list'] = [
      '#theme' => 'table',
      '#header' => [],
      '#rows' => $rows,
      '#empty' => $this->t("There is no documentation for this Partnership."),
    ];

    $build['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', [
        '%link' => $this->getFlow()->getLinkByStep(4, $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save')
          ->toString()
      ]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}

