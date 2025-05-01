<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "covered_by_plan",
 *   title = @Translation("Covered by Inspection Plan form.")
 * )
 */
class ParCoveredByPlanForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['covered_by_inspection', 'par_data_coordinated_business', 'covered_by_inspection', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must choose whether this member is covered by any of the inspection plans.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData(int $index = 1): void {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $covered_by = $coordinated_member->getBoolean('covered_by_inspection') ? 1 : 0;
      $this->getFlowDataHandler()->setFormPermValue('covered_by_inspection', $covered_by);
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function getElements(array $form = [], int $index = 1) {
    $coordinated_member_bundle = $this->getParDataManager()->getParBundleEntity('par_data_coordinated_business');

    // Inspection plan coverage.
    $form['covered_by_inspection'] = [
      '#type' => 'radios',
      '#title' => $this->t('Is this member covered by an inspection plan?'),
      '#title_tag' => 'h2',
      '#default_value' => $this->getDefaultValuesByKey('covered_by_inspection', $index, 1),
      '#options' => [
        1 => $coordinated_member_bundle->getBooleanFieldLabel('covered_by_inspection', TRUE),
        0 => $coordinated_member_bundle->getBooleanFieldLabel('covered_by_inspection', FALSE),
      ],
    ];

    return $form;
  }
}
