<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;

/**
 * A form for submission of upload of inspection plan documents.
 *
 * @ParForm(
 *   id = "inspection_plan",
 *   title = @Translation("Inspection plan upload form.")
 * )
 */
class ParInspectionPlanForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['notes', 'par_data_inspection_feedback', 'notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the details of this enquiry.'
    ]],
  ];

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_inspection_feedback = $this->getFlowDataHandler()->getParameter('par_data_inspection_feedback')) {
      $this->getFlowDataHandler()->setFormPermValue('notes', $par_data_inspection_feedback->get('notes')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $par_data_inspection_plan_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_inspection_plan', 'document');
    $field_definition = $par_data_inspection_plan_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    // External link for inspection plan templates.
    $options = ['attributes' => ['target' => '_blank'],
      'fragment' => 'templates'];
    $template_url = Url::fromUri('https://www.gov.uk/government/collections/primary-authority-documents', $options);

    $link = Link::fromTextAndUrl(t('Primary Authority templates'), $template_url)->toString();
    $help_text = $this->t('For inspection plan templates, go to: @link', ['@link' => $link]);
    $form['inspection_plan__type_help_text_link'] = [
      '#type' => 'markup',
      '#markup' => "<p>$help_text</p>",
    ];

    $form['inspection_plan_type_help_text'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('How to upload Primary Authority Inspection plans to Local Authorities'),
      '#description' => $this->t('To upload Primary Authority Inspection plans to a Local Authority, email it to <a href="mailto:pa@beis.gov.uk">pa@beis.gov.uk</a> with details of the organisation it applies to and we’ll get back to you shortly.'),
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide feedback'),
      '#default_value' => $this->getDefaultValuesByKey('notes', $cardinality),
      '#description' => '<p>Use this section to give feedback on this inspection plan, this will be submitted to the primary authority.</p>',
    ];

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload supporting documents (optional)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/inspection_plan/',
      '#multiple' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return $form;
  }
}
