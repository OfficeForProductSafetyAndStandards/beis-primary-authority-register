<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The advice document upload form.
 */
class ParPartnershipFlowsAdviceUploadForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return "Primary Authority Advice | Edit document type and regulatory functions";
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    if (isset($par_data_advice)) {
      $files = $par_data_advice->get('document')->referencedEntities();
      $ids = [];
      foreach ($files as $file) {
        $ids[] = $file->id();
      }
      $this->getFlowDataHandler()->setFormPermValue('files', $ids);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_advice);

    // PAR-1158 add the required external link for advice templates.
    $par_text = t('For advice templates, go to:');
    $options = ['attributes' => ['target' => '_blank'],
               'fragment' => 'templates'];
    $url_obj = Url::fromUri('https://www.gov.uk/government/collections/primary-authority-documents', $options);

    $link = Link::fromTextAndUrl(t('Primary Authority templates'), $url_obj)->toString();
    $form['advice_type_help_text_link'] = [
      '#type' => 'markup',
      '#markup' => "<p>{$par_text} {$link}</p>",
    ];

    $par_data_advice_fields = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions('par_data_advice', 'document');
    $field_definition = $par_data_advice_fields['document'];
    $file_extensions = $field_definition->getSetting('file_extensions');

    $form['advice_type_help_text'] = [
      '#type' => 'fieldset',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('How to upload Primary Authority Advice to Local Authorities'),
      '#description' => $this->t('To upload Primary Authority Advice to a Local Authority, email it to <a href="mailto:pa@beis.gov.uk">pa@beis.gov.uk</a> with details of the organisation it applies to and we’ll get back to you shortly.'),
    ];

    // Multiple file field.
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload file(s)'),
      '#description' => t('Use Ctrl or cmd to select multiple files'),
      '#upload_location' => 's3private://documents/advice/',
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("files"),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => $file_extensions
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

}
