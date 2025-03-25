<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Contact details form plugin.
 *
 * @ParForm(
 *   id = "contact_details_full",
 *   title = @Translation("Contact details full form.")
 * )
 */
class ParContactDetailsFullForm extends ParFormPluginBase {

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
    ['mobile_phone', 'par_data_person', 'mobile_phone', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the mobile phone number for this contact.'
    ]],
    ['email', 'par_data_person', 'email', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the email address for this contact.'
    ]],
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
      $this->setDefaultValuesByKey("salutation", $index, $par_data_person->get('salutation')->getString());
      $this->setDefaultValuesByKey("first_name", $index, $par_data_person->get('first_name')->getString());
      $this->setDefaultValuesByKey("last_name", $index, $par_data_person->get('last_name')->getString());
      $this->setDefaultValuesByKey("work_phone", $index, $par_data_person->get('work_phone')->getString());
      $this->setDefaultValuesByKey("mobile_phone", $index, $par_data_person->get('mobile_phone')->getString());
      $this->setDefaultValuesByKey("email", $index, $par_data_person->get('email')->getString());
      $this->setDefaultValuesByKey("notes", $index, $par_data_person->getPlain('communication_notes'));

      // Get preferred contact methods.
      $contact_options = [
        'communication_email' => $par_data_person->getBoolean('communication_email'),
        'communication_phone' => $par_data_person->getBoolean('communication_phone'),
        'communication_mobile' => $par_data_person->getBoolean('communication_mobile'),
      ];

      // Checkboxes works nicely with keys, filtering booleans for "1" value.
      $this->setDefaultValuesByKey('preferred_contact', $index, array_keys($contact_options, 1));

      // Provide an option to limit whether the email address can be entered.
      $limit_all_users = isset($this->getConfiguration()['limit_all_users']) ? (bool) $this->getConfiguration()['limit_all_users'] : FALSE;
      if ($limit_all_users) {
        $this->setDefaultValuesByKey("email_readonly", $index, TRUE);
      }
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $form['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#value' => $this->t('Enter the contact details'),
      '#attributes' => ['class' => ['govuk-heading-m']]
    ];

    $form['salutation'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title (optional)'),
      '#description' => $this->t('For example, Ms Mr Mrs Dr'),
      '#default_value' => $this->getDefaultValuesByKey('salutation', $index),
      '#attributes' => ['autocomplete' => 'honorific-prefix']
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the first name'),
      '#default_value' => $this->getDefaultValuesByKey('first_name', $index),
      '#attributes' => ['autocomplete' => 'given-name']
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the last name'),
      '#default_value' => $this->getDefaultValuesByKey('last_name', $index),
      '#attributes' => ['autocomplete' => 'family-name']
    ];

    $form['work_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Enter the work phone number'),
      '#default_value' => $this->getDefaultValuesByKey('work_phone', $index),
      '#attributes' => ['autocomplete' => 'tel']
    ];

    $form['mobile_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Enter the mobile phone number (optional)'),
      '#default_value' => $this->getDefaultValuesByKey('mobile_phone', $index),
      '#attributes' => ['autocomplete' => 'tel']
    ];

    // Prevent modifying of email address when un-editable.
    if ($this->getDefaultValuesByKey('email_readonly', $index, FALSE)) {
      $form['email_readonly'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['govuk-form-group']],
        'heading' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#attributes' => ['class' => ['govuk-heading-m']],
          '#value' => $this->t('Email address'),
        ],
        'description' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('You cannot update this person\'s email address because they already have an account.'),
        ],
        'email_address' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#attributes' => ['class' => ['govuk-inset-text']],
          '#value' => $this->getDefaultValuesByKey('email', $index),
        ],
      ];
      $form['email'] = [
        '#type' => 'hidden',
        '#value' => $this->getDefaultValuesByKey('email', $index),
        '#attributes' => ['autocomplete' => 'email']
      ];
    }
    else {
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Enter the email address'),
        '#default_value' => $this->getDefaultValuesByKey('email', $index),
        '#attributes' => ['autocomplete' => 'email']
      ];
    }

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
