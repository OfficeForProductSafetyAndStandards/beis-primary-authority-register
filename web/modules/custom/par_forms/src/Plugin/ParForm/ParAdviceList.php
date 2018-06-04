<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Advice list.
 *
 * @ParForm(
 *   id = "advice_list",
 *   title = @Translation("Lists advice in tabular format.")
 * )
 */
class ParAdviceList extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    // If a partnership parameter is set use this to get a list of advice.
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      if ($advice_list = $par_data_partnership->getAdvice()) {
        $this->getFlowDataHandler()->setParameter('advice_list', $advice_list);
      }
    }

    parent::loadData($cardinality);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['documentation_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#title' => 'Advice documentation',
      '#header' => [
        'Advice document download link(s)',
        'Type of document and regulatory functions',
      ],
      '#empty' => $this->t("There is no documentation for this partnership."),
    ];

    if ($advice_list = $this->getFlowDataHandler()->getParameter('advice_list')) {
      $advice_view_builder = $this->getParDataManager()->getViewBuilder('par_data_advice');

      foreach ($advice_list as $key => $advice) {
        $file_list = [];

        $advice_files = $advice->get('document')->referencedEntities();

        foreach ($advice_files as $file) {
          $file_list[] = $file->getFileName();
        }

        // The first column contains a rendered summary of the document.
        $advice_summary = $advice_view_builder->view($advice, 'summary');

        // The second column contains a summary of the confirmed details.
        $advice_details = '';
        $advice_type_value = $advice->get('advice_type')->getString();
        if ($advice_type = $advice->getTypeEntity()->getAllowedFieldlabel('advice_type', $advice_type_value)){
          $advice_details = "{$advice_type}";
          if ($regulatory_functions = $advice->getRegulatoryFunction()) {
            $advice_details .= " covering: " . PHP_EOL;
            $names = [];
            foreach ($regulatory_functions as $regulatory_function) {
              $names[] = $regulatory_function->get('function_name')->getString();
            }
            $advice_details .= implode(', ', $names);
          }
        }

        if ($advice_summary) {
          $form['documentation_list']['#rows'][$key] = [
            'data' => [
              'document' => $this->getRenderer()->render($advice_summary),
              'type' => $advice_details,
            ],
          ];
        }

        // Check permissions before adding the links for all operations.
        if ($this->getFlowNegotiator()->getFlowName() === 'partnership_authority') {

          // Create custom title element to add context to the edit link.
          $file_list_title = implode(", ", $file_list);

          // We need to create an array of all action links.
          $links = [
            [
              '#type' => 'markup',
              '#markup' => $this->getFlowNegotiator()->getFlow()
                ->getNextLink(
                  'edit',
                  ['par_data_advice' => $advice->id()],
                  ['attributes' => ['title' => "edit {$file_list_title}"]]
                )
                ->setText('edit')
                ->toString(),
            ],
          ];
          $form['documentation_list']['#rows'][$key]['data']['actions'] = $this->getRenderer()->render($links);
        }
      }
    }

    return $form;
  }
}
