<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * A demo multi-step form.
 */
class ParDemoFirstForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'example';

  public function getFormId() {
    return 'par_demo_first';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $retrieved = $this->getTempData();

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => $retrieved['name'] ?: '',
      '#required' => TRUE,
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // We can perform validation logic here.
    // This should only be form sanitisation though,
    // The real validation is handled at the entity
    // validation layer.
    // @see https://www.drupal.org/docs/8/api/entity-validation-api/entity-validation-api-overview
    $name = $form_state->getValue('name');
    $form_state->setValue('name', strtoupper($name));

    parent::validateForm($form, $form_state);
  }
}
