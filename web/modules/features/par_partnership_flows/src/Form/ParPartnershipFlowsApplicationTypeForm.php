<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * Partnership Application Form - Type radios page.
 */
class ParPartnershipFlowsApplicationTypeForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * Page title.
   *
   * @var ?string
   */
  protected $pageTitle = 'What kind of partnership are you applying for?';

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
      '#type' => 'container',
      '#attributes' => ['class' => 'govuk-form-group'],
    ];

    $form['application_type_fieldset']['application_type'] = [
      '#type' => 'radios',
      '#title' => 'Choose a type of partnership',
      '#title_tag' => 'h2',
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
      $id = $this->getElementId(['application_type'], $form);
      $form_state->setErrorByName($this->getElementName('application_type'), $this->wrapErrorMessage('You must select the type of application.', $id));
    }
  }

}
