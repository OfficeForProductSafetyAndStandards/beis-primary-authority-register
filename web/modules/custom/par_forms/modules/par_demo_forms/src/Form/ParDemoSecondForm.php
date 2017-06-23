<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * A demo multi-step form.
 */
class ParDemoSecondForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'pa';

  public function getFormId() {
    return 'par_demo_second';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $retrieved = $this->getTempData();

    $form['job'] = [
      '#type' => 'textfield',
      '#title' => t('Job'),
      '#default_value' => $retrieved['job'] ?: '',
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('par_demo_forms.third');

    parent::submitForm($form, $form_state);
  }
}
