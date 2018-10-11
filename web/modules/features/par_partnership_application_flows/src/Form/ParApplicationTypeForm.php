<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * Partnership Application Form - Type radios page.
 */
class ParApplicationTypeForm extends ParBaseForm {

  use ParFlowAccessTrait;

  protected $pageTitle = 'What kind of partnership are you applying for?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $types = [];
    foreach ($partnership_bundle->getAllowedValues('partnership_type') as $key => $type) {
      $types[$key] = "$type";
      switch ($key) {
        case 'direct':
          $types[$key] .= "<p class='form-hint'>For a partnership with a single business.</p>";

          break;
        case 'coordinated':
          $types[$key] .= "<p class='form-hint'>For a partnership with a trade association or other organisation to provide advice to a group of businesses.</p>";

          break;
      }
    }
    $this->getFlowDataHandler()->setFormPermValue('application_types', $types);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['application_type'] = [
      '#title' => 'Choose a type of partnership',
      '#description' => '<p>For more information visit the <a href="https://www.gov.uk/guidance/local-regulation-primary-authority#what-are-the-two-types-of-partnership" target="_blank">Primary Authority Guidance</a></p>',
      '#type' => 'radios',
      '#options' => $this->getFlowDataHandler()->getDefaultValues('application_types', []),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('application_type'),
      '#attributes' => ['class' => ['form-group']],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('application_type')) {
      $id = $this->getElementId(['application_type_fieldset'], $form);
      $form_state->setErrorByName($this->getElementName('application_type'), $this->wrapErrorMessage('Please select the type of application.', $id));
    }

    parent::validateForm($form, $form_state);
  }

}
