<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Date form plugin.
 *
 * @ParForm(
 *   id = "date",
 *   title = @Translation("Date.")
 * )
 */
class ParDateForm extends ParFormPluginBase {

  /**
   * @defaults
   */
  public function getFormDefaults() {
    return [
      'date' => ['year' => date('Y'), 'month' => date('m'), 'day' => date('d')],
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
    $form['date'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date'),
      '#description' => $this->t('For example: 01/01/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date', $index, $this->getFormDefaultByKey('date')),
    ];

    return $form;
  }
}
