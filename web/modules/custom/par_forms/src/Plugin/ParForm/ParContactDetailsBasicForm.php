<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details form plugin.
 *
 * @ParForm(
 *   id = "contact_details_basic",
 *   title = @Translation("Contact details basic form.")
 * )
 */
class ParContactDetailsBasicForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_person:person' => [
      'first_name' => 'first_name',
      'last_name' => 'last_name',
      'work_phone' => 'work_phone',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->setDefaultValuesByKey("first_name", $cardinality, $par_data_person->get('first_name')->getString());
      $this->setDefaultValuesByKey("last_name", $cardinality, $par_data_person->get('last_name')->getString());
      $this->setDefaultValuesByKey("work_phone", $cardinality, $par_data_person->get('work_phone')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the first name'),
      '#default_value' => $this->getDefaultValuesByKey('first_name', $cardinality),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the last name'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $cardinality),
    ];

    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the work phone number'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $cardinality),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(&$form_state, $cardinality = 1, array $violations = []) {
    // @todo create wrapper for setErrorByName as this is ugly creating a link.
    if (empty($form_state->getValue('first_name'))) {
      $form_state->setErrorByName('first_name', $this->t('<a href="#edit-first-name">The first name field is required.</a>'));
    }

    if (empty($form_state->getValue('last_name'))) {
      $form_state->setErrorByName('last_name', $this->t('<a href="#edit-last-name">The last name field is required.</a>'));
    }

    if (empty($form_state->getValue('work_phone'))) {
      $form_state->setErrorByName('work_phone', $this->t('<a href="#edit-work-phone">The work phone field is required.</a>'));
    }

    return parent::validate($form_state, $cardinality);
  }
}
