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
 *   id = "enquiry_detail",
 *   title = @Translation("The full enquiry display.")
 * )
 */
class ParEnquiryDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_general_enquiry = $this->getFlowDataHandler()->getParameter('par_data_general_enquiry');

    // If an enforcement notice parameter is set use this.
    if ($par_data_general_enquiry) {
      if ($par_data_general_enquiry->hasField('request_date')) {
        $this->getFlowDataHandler()->setFormPermValue("request_date", $par_data_general_enquiry->request_date->view('full'));
      }
      if ($par_data_general_enquiry->hasField('notes')) {
        $this->getFlowDataHandler()->setFormPermValue("notes", $par_data_general_enquiry->notes->view('full'));
      }
      if ($par_data_general_enquiry->hasField('document')) {
        $this->getFlowDataHandler()->setFormPermValue("document", $par_data_general_enquiry->document->view('full'));
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

    $form['general_enquiry'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['govuk-form-group']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary of enquiry'),
        '#attributes' => ['class' => 'govuk-heading-l'],
      ],
      'date' => $this->getDefaultValuesByKey('request_date', $index, NULL),
      'notes' => $this->getDefaultValuesByKey('notes', $index, NULL),
      'document' => $this->getDefaultValuesByKey('document', $index, NULL),
    ];

    // Add operation link for updating notice details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('request_details', 'Change this enquiry', $params);
      $form['general_enquiry']['change_notes'] = [
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
