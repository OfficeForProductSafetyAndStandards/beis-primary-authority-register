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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');

    $form['application_type_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['application_type_fieldset']['application_type'] = [
      '#title' => 'Choose a type of partnership',
      '#type' => 'radios',
      '#options' => $partnership_bundle->getAllowedValues('partnership_type'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('application_type'),
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
