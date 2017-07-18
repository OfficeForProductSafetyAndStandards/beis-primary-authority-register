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
      '#title' => t('Basic textfield'),
      '#description' => t('This is a textfield hint, please enter letters and spaces only. e.g. Jane Smith'),
    ];

    $form['textarea'] = [
      '#type' => 'textarea',
      '#title' => t('Basic textarea'),
      '#description' => t('This is a text area hint, please enter a few sentences.'),
    ];

    $form['file_upload'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload image'),
      '#progress_indictator' => 'none',
      '#upload_location' => 's3public://styleguide/',
      '#description' => t('This is an image hint, please select an image of type GIF, JPG or PNG.'),
    ];

    $form['select'] = [
      '#type' => 'select',
      '#title' => t('Basic select dropdown'),
      '#options' => [
        0 => t('No'),
        1 => t('Yes'),
        2 => t('Maybe'),
      ],
      '#description' => t('This is a select list hint, please choose an option.'),
    ];

    $form['radios_2'] = [
      '#type' => 'radios',
      '#title' => t('Do you already have a personal user account?'),
      '#options' => [
        0 => t('Yes'),
        1 => t('No')
      ],
      '#description' => t('This is a hint, if you already have a user account please select ‘Yes’.'),
    ];

    $form['radios_3'] = [
      '#type' => 'radios',
      '#title' => t('Where do you live?'),
      '#options' => [
        0 => t('Northern Ireland'),
        1 => t('Isle of Man or the Channel Islands'),
        2 => t('I am a British citizen living abroad')
      ],
      '#description' => t('This is a hint, choose an option.'),
    ];

    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => t('Send me a copy'),
    ];

    $form['checkboxes'] = [
      '#type' => 'checkboxes',
      '#title' => t('Which types of waste do you transport regularly?'),
      '#options' => [
        0 => t('Closed'),
        1 => t('Active'),
        2 => t('Dormant')
      ],
      '#attributes' => ['option_count' => 3],
      '#description' => t('This is a hint, choose whether you would like a copy of this form.'),
    ];

    $form['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => t('Fieldset example'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      'textfield_within_fieldset' => [
        '#type' => 'textfield',
        '#title' => t('Fieldset text field'),
        '#description' => t('This is a hint within a fieldset, enter some text into the box.'),
      ],
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next')
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Generate errors for all fields.
    $form_state->setErrorByName('textfield', $this->t("This is a test validation, the value %value for %field is invalid.", ['%value' => $form_state->getValue('textfield'), '%field' => 'textfield']));
    $form_state->setErrorByName('checkbox', $this->t("This is a test validation, the value %value for %field is invalid.", ['%value' => $form_state->getValue('checkbox'), '%field' => 'checkbox']));
    $form_state->setErrorByName('textarea', $this->t("This is a test validation, the value %value for %field is invalid.", ['%value' => $form_state->getValue('textarea'), '%field' => 'textarea']));
    $form_state->setErrorByName('radios_3', $this->t('This is a test validation for a radio without any option'));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t("This form submission was successful."));
  }
}
