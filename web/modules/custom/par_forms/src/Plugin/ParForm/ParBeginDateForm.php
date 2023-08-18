<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Member begin date form plugin.
 *
 * @ParForm(
 *   id = "begin_date",
 *   title = @Translation("Member begin date.")
 * )
 */
class ParBeginDateForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected array $entityMapping = [
    ['date_membership_began', 'par_data_coordinated_business', 'date_membership_began', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the date the membership began e.g. 2017 - 9 - 21.'
    ]],
  ];

  /**
   * @defaults
   */
  public function getFormDefaults() {
    return [
      'date_membership_began' => ['year' => date('Y'), 'month' => date('m'), 'day' => date('d')],
    ];
  }

  /**
   * Load the data for this form.
   */
  public function loadData(int $index = 1): void {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $this->getFlowDataHandler()->setFormPermValue('date_membership_began', $coordinated_member->get('date_membership_began')->getString());
    }

    parent::loadData($index);
  }

  /**
   * {@inheritdoc}
   */
  public function getElements(array $form = [], int $index = 1) {

    // Membership begin date.
    $form['date_membership_began'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date the membership began'),
      '#description' => $this->t('For example: 01/01/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date_membership_began', $index, $this->getFormDefaultByKey('date_membership_began')),
    ];

    return $form;
  }
}
