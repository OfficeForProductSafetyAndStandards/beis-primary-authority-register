<?php

namespace Drupal\par_demo_forms\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * A demo multi-step form.
 */
class ParDemoFirstForm extends FormBase {

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
    // If we are editing existing data we can load it here.
    // $entity = $this->entityManager->getStorage('par_entity')->load($id);
    // $this->setDataValue('name', $entity->get('name')->getValue());

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#required' => TRUE,
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
