<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormBuilder;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "partnership_type",
 *   title = @Translation("Partnership type form.")
 * )
 */
class ParPartnershipTypeForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['about_business', 'par_data_partnership', 'partnership_type', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'Please select the type of application.',
    ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $application_types = [];
    $application_type_descriptions = [];
    foreach ($partnership_bundle->getAllowedValues('partnership_type') as $key => $type) {
      $application_types[$key] = "$type";
      switch ($key) {
        case 'direct':
          $application_type_descriptions[$key] = "For a partnership with a single business.";

          break;

        case 'coordinated':
          $application_type_descriptions[$key] = "For a partnership with a trade association or other organisation to provide advice to a group of businesses.";

          break;
      }
    }
    $this->setDefaultValuesByKey("application_types", $index, $application_types);
    $this->setDefaultValuesByKey("application_type_descriptions", $index, $application_type_descriptions);

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {

    $form['application_type'] = [
      '#type' => 'radios',
      '#title' => 'Choose a type of partnership',
      '#title_tag' => 'h2',
      '#description' => 'For more information visit the <a href="https://www.gov.uk/guidance/local-regulation-primary-authority#what-are-the-two-types-of-partnership" target="_blank">Primary Authority Guidance</a>',
      '#options' => $this->getFlowDataHandler()->getDefaultValues('application_types', []),
      '#options_descriptions' => $this->getFlowDataHandler()->getDefaultValues('application_type_descriptions', []),
      '#after_build' => [
        [static::class, 'optionsDescriptions'],
      ],
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('application_type'),
      '#attributes' => ['class' => ['govuk-form-group']],
    ];

    return $form;
  }

  /**
   * Validate date field.
   */
  #[\Override]
  public function validate(array $form, FormStateInterface &$form_state, $index = 1, mixed $action = ParFormBuilder::PAR_ERROR_DISPLAY) {
    $person_id_key = $this->getElementKey('application_type');
    $value = $form_state->getValue($person_id_key);
    if (empty($value)) {
      $id_key = $this->getElementKey('application_type', $index, TRUE);
      $form_state->setErrorByName($this->getElementName($person_id_key), $this->wrapErrorMessage('Please select the type of application.', $this->getElementId($id_key, $form)));
    }

    parent::validate($form, $form_state, $index, $action);
  }

}
