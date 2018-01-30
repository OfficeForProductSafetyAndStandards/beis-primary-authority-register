<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * Partnership Application Form - Type radios page.
 */
class ParPartnershipFlowsApplicationTypeForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

//  protected $formItems = [
//    'par_data_partnership:partnership' => [
//      'about_partnership' => 'about_partnership',
//    ],
//  ];

  protected $pageTitle = 'What kind of partnership are you applying for?';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_type';
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->retrieveEditableValues();
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
    parent::validateForm($form, $form_state);
    if (!$form_state->getValue('application_type')) {
      $this->setElementError('application_type', $form_state, 'Please select the type of application.');
    }
  }

}
