<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Inspection plan feedback details form plugin.
 *
 * @ParForm(
 *   id = "inspection_feedback_detail",
 *   title = @Translation("The full inspection feedback display.")
 * )
 */
class ParInspectionFeedbackDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback');

    // If an enforcement notice parameter is set use this.
    if ($par_data_inspection_feedback) {
      if ($par_data_inspection_feedback->hasField('request_date')) {
        $this->getFlowDataHandler()->setFormPermValue("request_date", $par_data_inspection_feedback->request_date->view('full'));
      }
      if ($par_data_inspection_feedback->hasField('notes')) {
        $this->getFlowDataHandler()->setFormPermValue("notes", $par_data_inspection_feedback->notes->view('full'));
      }
      if ($par_data_inspection_feedback->hasField('document')) {
        $this->getFlowDataHandler()->setFormPermValue("document", $par_data_inspection_feedback->document->view('full'));
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());
    $params = $this->getRouteParams() + ['destination' => $return_path];

    $form['inspection_feedback'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['form-group']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary of feedback'),
        '#attributes' => ['class' => 'govuk-heading-l'],
      ],
      'date' => $this->getDefaultValuesByKey('request_date', $index, NULL),
      'notes' => $this->getDefaultValuesByKey('notes', $index, NULL),
      'document' => $this->getDefaultValuesByKey('document', $index, NULL),
    ];

    // Add operation link for updating notice details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('request_details', 'Change the details about this request', $params);
      $form['inspection_feedback']['change_notes'] = [
        '#type' => 'markup',
        '#weight' => 99,
        '#markup' => t('@link', [
          '@link' => $link ? $link->toString() : '',
        ]),
      ];
    }
    catch (ParFlowException $e) {

    }


    return $form;
  }
}
