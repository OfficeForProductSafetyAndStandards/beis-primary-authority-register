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
  protected $flow = 'example';

  public function getFormId() {
    return 'par_demo_second';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['file'] = [
      '#type' => 'file',
      '#title' => t('Upload a file'),
      '#default_value' => $this->getFormDataByKey('file'),
    ];

    $form['nested']['file2'] = [
      '#type' => 'file',
      '#title' => t('Upload a file (nested)'),
      '#default_value' => isset($retrieved['file']) ? ['fids' => $retrieved['file']] : '',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    return $form;
  }
}
