<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\node\NodeInterface;

/**
 * A demo multi-step form.
 */
class ParDemoOverviewForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'example';

  public function getFormId() {
    return 'par_demo_overview';
  }

  /**
   * {@inheritdoc}
   *
   * @example
   * if ($node) {
   *   // If we're editing an entity we should set the state
   *   // to something other than default to avoid conflicts
   *   // with existing versions of the same form.
   *   $this->setState("edit:{$node->id()}");
   * }
   *
   * @example
   * if ($node) {
   *   // If we want to use values already saved we have to tell
   *   // the form about them.
   *   $this->loadDataValue('name', $node->getTitle());
   *   $this->loadDataValue('hobby', $node->get('body')->getValue());
   * }
   *
   * @example
   * // You could stub a default value like so.
   * // It is probably better to only stub data
   * // when a form is being edited.
   * $stubbed = \Drupal::config('par_data_entities.settings')->get('stubbed');
   * $stubs = [
   *   'name' => $node && $stubbed === TRUE ? 'Jo Smith' : '',
   *   'hobby' => $node && $stubbed === TRUE ? 'Coding' : '',
   * ];
   * // This can be used like so.
   * $this->getDefaultValues('name', $stubs['name']);
   * $this->getDefaultValues('hobby', $stubs['hobby']);
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    if ($node) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$node->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('name', $node->getTitle());
      $this->loadDataValue('hobby', $node->get('body')->getValue());
    }


    // Section 1.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#title' => t('This is the first section'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['first_section']['name'] = [
      '#type' => 'markup',
      '#markup' => t('Name: %name', ['%name' => $this->getDefaultValues('name', '', $this->getFlow()->getFormIdByStep(2))]),
    ];
    // We can get a link to a given form step like so.
    $form['first_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(2)->setText('Name')->toString()]),
    ];

    // Section 2.
    $form['second_section'] = [
      '#type' => 'fieldset',
      '#title' => t('This is the second section'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['second_section']['name'] = [
      '#type' => 'markup',
      '#markup' => t('Files: %files', ['%files' => 'You have some files']),
    ];
    // We can get a link to a given form step like so.
    $form['second_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(3)->setText('Files')->toString()]),
    ];

    // Section 3.
    $form['third_section'] = [
      '#type' => 'fieldset',
      '#title' => t('This is the third section'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['third_section']['hobby'] = [
      '#type' => 'markup',
      '#markup' => t('Hobbies: %hobbies', ['%hobbies' => $this->getDefaultValues('hobby', '', $this->getFlow()->getFormIdByStep(4))]),
    ];
    // We can get a link to a given form step like so.
    $form['third_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(4)->setText('Hobbies')->toString()]),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];
    // We can get a link to a custom route like so.
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('%link', ['%link' => $this->getLinkByRoute('<front>')->setText('Cancel')->toString()]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // We're not in kansas any more, after submitting the overview let's go home.
    $form_state->setRedirect('<front>');
  }

}
