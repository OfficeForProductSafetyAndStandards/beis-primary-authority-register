<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormBuilder;
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
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['first_name', 'par_data_person', 'first_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the first name for this contact.'
    ]],
    ['last_name', 'par_data_person', 'last_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the last name for this contact.'
    ]],
    ['work_phone', 'par_data_person', 'work_phone', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the work phone number for this contact.'
    ]],
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
}
