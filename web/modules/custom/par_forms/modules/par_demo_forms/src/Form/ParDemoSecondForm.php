<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;
use Drupal\node\NodeInterface;

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
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if ($node) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      // You must remember to set the state in the same way
      // on all forms in any given flow.
      $this->setState("edit:{$node->id()}");
    }

    $form['file'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload files'),
      '#multiple' => true,
      '#upload_location' => 's3private://documents/file/',
      '#default_value' => $this->getDefaultValues('file', []),
    ];

    $form['nested']['file2'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload a file (nested)'),
      '#upload_location' => 's3private://documents/file2/',
      '#default_value' => $this->getDefaultValues('file2', []),
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
    parent::submitForm($form, $form_state);

    // After each child formo we go back to the overview.
    $form_state->setRedirect('par_demo_forms.overview', $this->getRouteParams());
  }
}
