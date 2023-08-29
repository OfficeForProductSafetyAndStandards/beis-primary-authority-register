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
 * Deviation request details form plugin.
 *
 * @ParForm(
 *   id = "deviation_request_detail",
 *   title = @Translation("The full deviation request display.")
 * )
 */
class ParDeviationRequestDetail extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    $par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request');

    // If an enforcement notice parameter is set use this.
    if ($par_data_deviation_request) {
      if ($par_data_deviation_request->hasField('request_date')) {
        $this->setDefaultValuesByKey("request_date", $index, $par_data_deviation_request->request_date->view('full'));
      }
      if ($par_data_deviation_request->hasField('notes')) {
        $this->setDefaultValuesByKey("notes", $index, $par_data_deviation_request->notes->view('full'));
      }
      if ($par_data_deviation_request->hasField('document')) {
        $this->setDefaultValuesByKey("document", $index, $par_data_deviation_request->document->view('full'));
      }
      $this->setDefaultValuesByKey("deviation_status", $index, $par_data_deviation_request->getParStatus());

      if ($notes = $par_data_deviation_request->getPlain('primary_authority_notes')) {
        $this->setDefaultValuesByKey("deviation_status_notes", $index, $notes);
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

    $form['deviation_request'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['form-group']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Summary of deviation request'),
        '#attributes' => ['class' => 'heading-large'],
      ],
      'status' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->getDefaultValuesByKey('deviation_status', $index) . $this->getDefaultValuesByKey('deviation_status_notes', $index, ''),
      ],
      'date' => $this->getDefaultValuesByKey('request_date', $index, NULL),
      'notes' => $this->getDefaultValuesByKey('notes', $index, NULL),
      'document' => $this->getDefaultValuesByKey('document', $index, NULL),
    ];

    // Add operation link for updating request details.
    try {
      $link = $this->getFlowNegotiator()->getFlow()
        ->getOperationLink('request_details', 'Change the details about this request', $params);
      $form['deviation_request']['change_notes'] = [
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
