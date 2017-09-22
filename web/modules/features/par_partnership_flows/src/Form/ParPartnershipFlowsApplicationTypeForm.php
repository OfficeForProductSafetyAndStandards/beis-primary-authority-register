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

    $form['application_type_fieldset'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['application_type_fieldset']['application_type'] = [
      '#title' => 'Type of application',
      '#type' => 'radios',
      '#options' => [
        'direct' => 'Direct Partnership',
        'coordinated' => 'Co-ordinated Partnership',
      ],
      '#default_value' => $this->getDefaultValues('application_type'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

  }

}
