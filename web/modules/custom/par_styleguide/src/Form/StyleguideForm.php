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

    $form['status_messages'] = [
      '#type' => 'status_messages',
    ];

    $form['textfield'] = [
      '#type' => 'textfield',
      '#title' => t('Basic textfield'),
      '#required' => TRUE,
    ];
    $form['textarea'] = [
      '#type' => 'textarea',
      '#title' => t('Basic textarea'),
    ];

    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => t('Boolean checkbox'),
    ];
    $form['checkboxes'] = [
      '#type' => 'checkboxes',
      '#options' => [
        'one' => $this->t('One'),
        'two' => $this->t('Two'),
        'three' => $this->t('Three'),
        'four' => $this->t('Four'),
        'five' => $this->t('Five')
      ],
      '#title' => $this->t('Multiple checkboxes'),
    ];

    $form['radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Radios'),
      '#default_value' => 1,
      '#options' => [
        'one' => $this->t('One'),
        'two' => $this->t('Two'),
        'three' => $this->t('Three'),
        'four' => $this->t('Four'),
        'five' => $this->t('Five')
      ],
    ];

    $form['select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select element'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => [
          '2.1' => $this->t('Two point one'),
          '2.2' => $this->t('Two point two'),
        ],
        '3' => $this->t('Three'),
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit button'),
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
