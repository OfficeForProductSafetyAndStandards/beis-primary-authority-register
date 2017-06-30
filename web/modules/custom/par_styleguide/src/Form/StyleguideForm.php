<?php

namespace Drupal\par_styleguide\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

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
        '#title' => t('Keywords'),
        '#type' => 'textarea',
        '#description' => t('The comment will be unpublished if it contains any of the phrases above. Use a case-sensitive, comma-separated list of phrases. Example: funny, bungee jumping, "Company, Inc."'),
    ];
    
    $form['file_upload'] = [
        '#title' => t('Image'),
        '#type' => 'managed_file',
        '#progress_indictator' => 'none',
        '#description' => t('The uploaded image will be displayed on this page using the image style choosen below.'),
        '#upload_location' => 's3public://styleguide/',
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
