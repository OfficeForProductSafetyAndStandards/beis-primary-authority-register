<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The inspection plan document upload form.
 */
class ParPartnershipFlowsInspectionPlanUploadForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $verb = 'Upload';
    $this->pageTitle = "$verb inspection plan documents";

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataInspectionPlan $par_data_inspection_plan
   *   The inspection plan being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    if (isset($par_data_inspection_plan)) {
      $files = $par_data_inspection_plan->get('document')->referencedEntities();
      $ids = [];
      foreach ($files as $file) {
        $ids[] = $file->id();
      }
      $this->getFlowDataHandler()->setFormPermValue('upload', $ids);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_inspection_plan);

    // External link for inspection plan templates.
    $par_text = t('For inspection plan templates, go to: ');
    $options = ['attributes' => ['target' => '_blank'],
               'fragment' => 'templates'];
    $url_obj = Url::fromUri('https://www.gov.uk/government/collections/primary-authority-documents', $options);

    $link = Link::fromTextAndUrl(t('Primary Authority templates'), $url_obj)->toString();
    $output = $par_text . $link;
    $form['inspection_plan__type_help_text_link'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$output}</p>",
    ];

    $par_data_inspection_plan_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_inspection_plan', 'document');
    $field_definition = $par_data_inspection_plan_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    $form['inspection_plan_type_help_text'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('How to upload Primary Authority Inspection plans to Local Authorities'),
      '#description' => $this->t('To upload Primary Authority Inspection plans to a Local Authority, email it to <a href="mailto:pa@beis.gov.uk">pa@beis.gov.uk</a> with details of the organisation it applies to and we’ll get back to you shortly.'),
    ];

    // Multiple file field.
    $form['inspection_plan_files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload file(s)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/inspection_plan/',
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("inspection_plan_files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

}
