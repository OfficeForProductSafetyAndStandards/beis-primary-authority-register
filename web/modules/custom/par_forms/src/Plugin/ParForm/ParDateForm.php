<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
  public function loadData($cardinality = 1) {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $this->getFlowDataHandler()->setFormPermValue('date_membership_began', $coordinated_member->get('date_membership_began')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    // Membership begin date.
    $form['date'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date'),
      '#description' => $this->t('For example: 01/01/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date', $cardinality, $this->getFormDefaultByKey('date')),
    ];

    return $form;
  }
}
