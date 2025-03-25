<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details form plugin.
 *
 * @ParForm(
 *   id = "contact_preferences",
 *   title = @Translation("Contact preferences form.")
 * )
 */
class ParContactPreferencesForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['notes', 'par_data_person', 'communication_notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter any communication notes that are relevant to this contact.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->setDefaultValuesByKey("notes", $index, $par_data_person->getPlain('communication_notes'));

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->setDefaultValuesByKey('preferred_contact', $index, array_keys($contact_options, 1));
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    // Get preferred contact methods labels.
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $contact_options = [
      'communication_email' => $person_bundle->getBooleanFieldLabel('communication_email', 'on'),
      'communication_phone' => $person_bundle->getBooleanFieldLabel('communication_phone', 'on'),
      'communication_mobile' => $person_bundle->getBooleanFieldLabel('communication_mobile', 'on'),
    ];

    $form['preferred_contact'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the preferred methods of contact (optional)'),
      '#title_tag' => 'h2',
      '#options' => $contact_options,
      '#default_value' => $this->getDefaultValuesByKey('preferred_contact', $index, []),
      '#return_value' => 'on',
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide contact notes (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('notes', $index),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    return $form;
  }
}
