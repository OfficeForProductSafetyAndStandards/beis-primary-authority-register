<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Select an authority type.
 *
 * @ParForm(
 *   id = "authority_type",
 *   title = @Translation("Select an authority type form.")
 * )
 */
class ParAuthorityTypeForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['authority_type', 'par_data_authority', 'authority_type', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must choose which type of authority is most relevant.',
    ],
    ],
  ];

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    $par_data_authority = $this->getFlowDataHandler()->getParameter('par_data_authority');
    if ($par_data_authority) {
      $this->getFlowDataHandler()->setFormPermValue("authority_type", $par_data_authority->get('authority_type')->getString());
    }

    $authority_bundle = $this->getParDataManager()->getParBundleEntity('par_data_authority');
    $types = $authority_bundle->getAllowedValues('authority_type');

    if ($types) {
      $this->getFlowDataHandler()
        ->setFormPermValue("authority_type_options", (array) $types);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $form['authority_type'] = [
      '#type' => 'radios',
      '#title' => 'Choose an authority type',
      '#title_tag' => 'h2',
      '#attributes' => ['class' => ['govuk-form-group']],
      '#options' => $this->getFlowDataHandler()->getFormPermValue('authority_type_options'),
      '#default_value' => $this->getDefaultValuesByKey('authority_type', $index, NULL),
    ];

    return $form;
  }

}
