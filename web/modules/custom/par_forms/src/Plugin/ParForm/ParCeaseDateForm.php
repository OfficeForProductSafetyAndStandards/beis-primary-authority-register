<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Member begin date form plugin.
 *
 * @ParForm(
 *   id = "cease_date",
 *   title = @Translation("Member cease date.")
 * )
 */
class ParCeaseDateForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['date_membership_ceased', 'par_data_coordinated_business', 'date_membership_ceased', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter the date the membership was ceased e.g. 2017 - 9 - 21.'
    ]],
  ];

  /**
   * @defaults
   */
  public function getFormDefaults() {
    return [
      'date_membership_ceased' => ['year' => date('Y'), 'month' => date('m'), 'day' => date('d')],
    ];
  }

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($coordinated_member = $this->getFlowDataHandler()->getParameter('par_data_coordinated_business')) {
      $this->getFlowDataHandler()->setFormPermValue('date_membership_ceased', $coordinated_member->get('date_membership_ceased')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    // Membership begin date.
    $form['date_membership_ceased'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date the membership ceased'),
      '#description' => $this->t('For example: 29/4/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date_membership_ceased', $cardinality, $this->getFormDefaultByKey('date_membership_ceased')),
    ];

    return $form;
  }
}
