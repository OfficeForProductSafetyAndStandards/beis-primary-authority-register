<?php

namespace Drupal\par_flow_transition_partnership_details\Controller;

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

    // Organisation.
    $par_data_organisation = current($par_data_partnership->get('field_organisation')->referencedEntities());

    $organisation_name = $par_data_organisation->get('name')->getString();

    $build['intro']['help_text_1'] = [
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => t('Review and confirm your documents for @organisation are still relevant', ['@organisation' => $organisation_name])
    ];

    $build['intro']['help_text_2'] = [
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => t('You don\'t have to do it all in one go')
    ];

    $build['intro']['help_text_3'] = [
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => t('You can continue to make changes to your information until 14 September 2017')
    ];

    // Show the documents in table format.
    $build['documentation_list'] = [
      '#theme' => 'table',
      '#title' => 'Advice documentation',
      '#header' => ['Document', 'Type of document and regulatory functions', 'Actions', 'Confirmed'],
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    // Get each Advice document and add as a table row.
    foreach ($par_data_partnership->getAdvice() as $advice) {
      $advice_view_builder = $advice->getViewBuilder();

      // The first column contains a rendered summary of the document.
      $advice_summary = $advice_view_builder->view($advice, 'summary');

      // The second column contains a summary of the confirmed details.
      $advice_details = 'Awaiting confirmation';
      if ($advice_type = $advice->get('advice_type')->getString()) {
        $advice_details = "{$advice_type}";
        if ($we_create_reference_between_documents_and_regulatory_function = FALSE) {
          $advice_details .= "covering:" . PHP_EOL . PHP_EOL;
        }
      }

      // The third column contains a list of actions that can be performed on this document.
      $links = [
        [
          '#type' => 'markup',
          '#markup' => $this->getFlow()
            ->getLinkByStep(10, ['par_data_advice' => $advice->id()])
            ->setText('edit')
            ->toString(),
        ],
      ];
      $advice_actions = $this->getRenderer()->render($links);

      // Fourth column contains the completion status of the document.
      $completion = $advice->getCompletionPercentage();

      if ($advice_summary && $advice_details && $advice_actions && $completion) {
        $build['documentation_list']['#rows'][] = [
          'data' => [
            'document' => $this->getRenderer()->render($advice_summary),
            'type' => $advice_details,
            'actions' => $advice_actions,
            'confirmed' => $this->renderPercentageTick($completion),
          ],
        ];
      }

      // Make sure to add the document cacheability data to this form.
      $this->addCacheableDependency($advice);
    }

    $build['cancel'] = [
      '#type' => 'markup',
      '#prefix' => '<br>',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getLinkByStep(3, $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save')
          ->toString(),
      ]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::build($build);

  }

}
