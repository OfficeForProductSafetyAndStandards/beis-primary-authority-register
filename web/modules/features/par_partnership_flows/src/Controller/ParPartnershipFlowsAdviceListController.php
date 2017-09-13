<?php

namespace Drupal\par_partnership_flows\Controller;

use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for rendering a list of advice documents.
 */
class ParPartnershipFlowsAdviceListController extends ParBaseController {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function content(ParDataPartnership $par_data_partnership = NULL) {
    $advice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_advice');

    // Show the documents in table format.
    $build['documentation_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#title' => 'Advice documentation',
      '#header' => [
        'Document',
        'Type of document and regulatory functions',
        'Actions',
      ],
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    // Get each Advice document and add as a table row.
    foreach ($par_data_partnership->getAdvice() as $advice) {
      $advice_view_builder = $this->getParDataManager()->getViewBuilder('par_data_advice');

      // The first column contains a rendered summary of the document.
      $advice_summary = $advice_view_builder->view($advice, 'summary');

      // The second column contains a summary of the confirmed details.
      $advice_details = '';
      $advice_type_value = $advice->retrieveStringValue('advice_type');
      if ($advice_type = $advice->getTypeEntity()->getAllowedFieldlabel('advice_type', $advice_type_value)){
        $advice_details = "{$advice_type}";
        if ($regulatory_functions = $advice->getRegulatoryFunction()) {
          $advice_details .= " covering: " . PHP_EOL;
          $names = [];
          foreach ($regulatory_functions as $regulatory_function) {
            $names[] = $regulatory_function->retrieveStringValue('function_name');
          }
          $advice_details .= implode(', ', $names);
        }
      }

      // The third column contains a list of actions that can be performed on
      // this document.
      $links = [
        [
          '#type' => 'markup',
          '#markup' => $this->getFlow()
            ->getNextLink('edit', ['par_data_advice' => $advice->id()])
            ->setText('edit')
            ->toString(),
        ],
      ];

      if ($advice_summary) {
        $build['documentation_list']['#rows'][] = [
          'data' => [
            'document' => $this->getRenderer()->render($advice_summary),
            'type' => $advice_details,
            'actions' => $this->getRenderer()->render($links),
          ],
        ];
      }

      // Make sure to add the document cacheability data to this form.
      $this->addCacheableDependency($advice);
      $this->addCacheableDependency(current($advice->retrieveEntityValue('document')));
    }

    $build['upload'] = [
      '#type' => 'markup',
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('advice_upload', $this->getRouteParams())
          ->setText('Upload a document')
          ->toString(),
      ]),
    ];

    $build['save'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('next', $this->getRouteParams(), ['attributes' => ['class' => 'button']])
          ->setText('Save')
          ->toString(),
      ]),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($advice_bundle);

    return parent::build($build);

  }

}
