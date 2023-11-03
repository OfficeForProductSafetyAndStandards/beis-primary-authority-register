<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  protected array $entityMapping = [
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
  public function loadData(int $index = 1): void {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->setDefaultValuesByKey("first_name", $index, $par_data_person->get('first_name')->getString());
      $this->setDefaultValuesByKey("last_name", $index, $par_data_person->get('last_name')->getString());
      $this->setDefaultValuesByKey("work_phone", $index, $par_data_person->get('work_phone')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the first name'),
      '#default_value' => $this->getDefaultValuesByKey('first_name', $index),
      '#attributes' => ['autocomplete' => 'given-name']
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the last name'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $index),
      '#attributes' => ['autocomplete' => 'family-name']
    ];

    $form['work_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Enter the work phone number'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $index),
      '#attributes' => ['autocomplete' => 'tel']
    ];

    return $form;
  }
}
