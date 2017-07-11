<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;

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
   * $entity = $this->entityManager->getStorage('par_entity')->load($id);
   * $this->setDataValue('name', $entity->get('name')->getValue());
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // If we are editing existing data we can load it here.
    $data = $this->getAllTempData();

    // Section 1.
    $form['first_section'] = [
      '#type' => 'fieldset',
      '#title' => t('This is the first section'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['first_section']['name'] = [
      '#type' => 'markup',
      '#markup' => t('Name: %name', ['%name' => $data['name']]),
    ];
    // We can get a link to a given form step like so.
    $form['first_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(2)->setText('Name')->toString()]),
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
      '#markup' => t('<br>Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(3)->setText('Files')->toString()]),
    ];

    // Section 3.
    $form['third_section'] = [
      '#type' => 'fieldset',
      '#title' => t('This is the third section'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    $form['third_section']['name'] = [
      '#type' => 'markup',
      '#markup' => t('Hobbies: <br>%hobbies', ['%hobbies' => $data['hobby']]),
    ];
    // We can get a link to a given form step like so.
    $form['third_section']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('<br>Edit: %link', ['%link' => $this->getFlow()->getLinkByStep(4)->setText('Hobbies')->toString()]),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];
    // We can get a link to a custom route like so.
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $this->getLinkByRoute('<front>')->setText('Cancel')->toString()]),
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
