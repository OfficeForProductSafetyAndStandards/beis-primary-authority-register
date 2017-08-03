<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;

/**
 * A controller for rendering a list of advice documents
 * relevant to a partnership.
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

    // Table headers.
    $header = ['data' => 'Document',
              'Type of document and regulatory functions',
              'Actions',
              'Confirmed'];

    $rows = [];

    // Organisation summary.
    $documents = $par_data_partnership->get('advice')->referencedEntities();

    foreach ($documents as $document) {
      $document_view_builder = $document->getViewBuilder();
      // The first column contains a rendered summary of the document.
      $document_summary = $this->renderMarkupField($document_view_builder->view($document, 'summary'));

      // The second column contains a summary of the confirmed details.
      $document_details = 'Awaiting confirmation';
      if ($advice_type = $document->get('advice_type')->getString()) {
        $document_details = "{$advice_type}";
        if ($we_create_reference_between_documents_and_regulatory_function = FALSE) {
          $document_details .= "covering:" . PHP_EOL . PHP_EOL;
        }
      }

      // The third column contains a list of actions that can be performed on this document.
      $links = [
        [
          '#type' => 'markup',
          '#markup' => $this->getFlow()
            ->getLinkByStep(10, ['par_data_advice' => $document->id()])
            ->setText('edit')
            ->toString(),
        ]
      ];
      $document_actions = $this->getRenderer()->render($links);

      // Fourth column contains the completion status of the document.
      $completion = $document->getCompletionPercentage();

      if ($document_summary && $document_details && $document_actions && $completion) {
        $rows = [
          'data' => [
            "Documents are not yet attached...",
            $document_details,
            $document_actions,
            $this->renderPercentageTick($completion)
          ]
        ];
      }

    }

    // Organisation.
    $par_data_organisation = current($par_data_partnership->get('organisation')->referencedEntities());

    $organisation_name = $par_data_organisation->get('name')->getString();

    $build['intro']['help_text_1'] = [
      '#type' => 'markup',
      '#markup' => t('<p>Review and confirm your documents for @organisation are still relevant</p>', ['@organisation' => $organisation_name])
    ];

    $build['intro']['help_text_2'] = [
      '#type' => 'markup',
      '#markup' => t('<p>You don\'t have to do it all in one go</p>')
    ];

    $build['intro']['help_text_3'] = [
      '#type' => 'markup',
      '#markup' => t('<p>You can continue to make changes to your information until 14 September 2017</p>')
    ];

    // Show the task links in table format.
    $build['task_list'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    $build['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>@link', [
        '@link' => $this->getFlow()->getLinkByStep(3, $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save')
          ->toString()
      ]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}

