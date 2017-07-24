<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;
use Drupal\node\NodeInterface;

/**
 * A demo multi-step form.
 */
class ParDemoThirdForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'example';

  public function getFormId() {
    return 'par_demo_third';
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

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('hobby', $node->get('body')->getValue());
    }

    $form['hobby'] = [
      '#type' => 'textarea',
      '#title' => t('Hobbies'),
      '#default_value' => $this->getDefaultValues('hobby'),
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
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
