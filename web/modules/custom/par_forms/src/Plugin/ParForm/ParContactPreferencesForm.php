<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  protected $entityMapping = [
    ['notes', 'par_data_person', 'communication_notes', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter any communication notes that are relevant to this contact.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $this->setDefaultValuesByKey("notes", $cardinality, $par_data_person->get('communication_notes')->getString());

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->setDefaultValuesByKey('preferred_contact', $cardinality, array_keys($contact_options, 1));
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
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
      '#options' => $contact_options,
      '#default_value' => $this->getDefaultValuesByKey('preferred_contact', $cardinality, []),
      '#return_value' => 'on',
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide contact notes (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('notes', $cardinality),
      '#description' => 'Add any additional notes about how best to contact this person.',
    ];

    return $form;
  }
}
