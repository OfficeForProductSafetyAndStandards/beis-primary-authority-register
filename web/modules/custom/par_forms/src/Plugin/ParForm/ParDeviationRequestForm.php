<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * A form for submission of general enquiries.
 *
 * @ParForm(
 *   id = "deviation_request",
 *   title = @Translation("Deviation request form.")
 * )
 */
class ParDeviationRequestForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['notes', 'par_data_deviation_request', 'notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the details of this enquiry.'
    ]],
    ['files', 'par_data_deviation_request', 'document', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must submit a proposed inspection plan for this enquiry.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData(int $index = 1): void {
    if ($par_data_deviation_request = $this->getFlowDataHandler()->getParameter('par_data_deviation_request')) {
      $this->getFlowDataHandler()->setFormPermValue('notes', $par_data_deviation_request->getPlain('notes'));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    $par_data_deviation_request_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_deviation_request', 'document');
    $field_definition = $par_data_deviation_request_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide a reason for deviation'),
      '#title_tag' => 'h2',
      '#default_value' => $this->getDefaultValuesByKey('notes', $index),
      '#description' => '<p>Use this section to give a reason for wanting to deviate from the inspection plan, this will be reviewed by the primary authority.</p>',
    ];

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload the proposed inspection plan'),
      '#title_tag' => 'h2',
      '#description' => t('Submit your proposed inspection plan to the primary auithority'),
      '#upload_location' => 's3private://documents/deviation_request/',
      '#multiple' => FALSE,
      '#default_value' => $this->getDefaultValuesByKey("files", $index),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return $form;
  }
}
