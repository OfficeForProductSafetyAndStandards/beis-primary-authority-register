<?php

namespace Drupal\par_styleguide\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Styleguide form controller for visualising rendered form elements.
 *
 * This form controller has no functional purpose within the application.
 */
class StyleguideForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_styleguide_form_controller';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['textfield'] = [
      '#type' => 'textfield',
      '#placeholder' => 'Placeholder text',
      '#title' => t('Basic textfield'),
    ];

    $form['textarea'] = [
        '#title' => t('Basic Textarea'),
        '#type' => 'textarea',
        '#placeholder' => 'Placeholder text',
        '#description' => t('The comment will be unpublished if it contains any of the phrases above. Use a case-sensitive, comma-separated list of phrases. Example: funny, bungee jumping, "Company, Inc."'),
    ];
    
    $form['file_upload'] = [
        '#title' => t('Upload Image'),
        '#type' => 'managed_file',
        '#progress_indictator' => 'none',
        '#description' => t('Please select an image of type GIF, JPG or TIFF.'),
        '#upload_location' => 's3public://styleguide/',
    ];
    
    $form['selected'] = [
        '#type' => 'select',
        '#title' => t('Basic Select Dropdown'),
        '#options' => [
            0 => t('No'),
            1 => t('Yes'),
            2 => t('Maybe'),
        ],
        '#description' => t('Set this to Yes if you would like this category to be selected by default.'),
    ];
    
    $form['radios'] = array(
        '#type' => 'radios',
        '#title' => t('Poll status'),
        '#options' => array(0 => t('Closed'), 1 => t('Active'), 2 => t('Dormant')),
        '#description' => t('When a poll is closed, visitors can no longer vote for it.'),
    );
    
    $form['checkbox'] = array(
        '#type' => 'checkbox',
        '#title' => t('Send me a copy.'),
    );
    
    $form['checkboxes'] = array(
        '#type' => 'checkboxes',
        '#options' => array(0 => t('Closed'), 1 => t('Active'), 2 => t('Dormant')),
        '#title' => t('What standardized tests did you take?'),
    );
    
    $form['fieldset'] = [
        '#type' => 'fieldset_example',
        '#title' => t('Fieldset example'),
        '#weight' => 5,
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'textfield_within_fieldset' => [
            '#type' => 'textfield',
            '#placeholder' => 'Placeholder text',
            '#title' => t('Fieldset text field'),
        ],
    ];
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Generate errors for all fields.
    $form_state->setErrorByName('textfield', $this->t("This is a test validation, the value '%value' is invalid.", array('%value' => $form_state->getValue('textfield'))));
    $form_state->setErrorByName('checkbox', $this->t("This is a test validation, the value '%value' is invalid.", array('%value' => $form_state->getValue('textfield'))));
    $form_state->setErrorByName('textarea', $this->t("This is a test validation, the value '%value' is invalid.", array('%value' => $form_state->getValue('textfield'))));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t("This form submission was sucessful."));
  }
}
