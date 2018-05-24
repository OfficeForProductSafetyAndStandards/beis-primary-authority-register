<?php

namespace Drupal\par_forms\Plugin\ParForm;

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
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_coordinated_business:coordinated_business' => [
      'date_membership_began' => 'date_membership_began',
    ],
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
    $form['date_membership_began'] = [
      '#type' => 'gds_date',
      '#title' => $this->t('Enter the date the membership began'),
      '#description' => $this->t('For example: 29/4/2010'),
      '#default_value' => $this->getDefaultValuesByKey('date_membership_began', $cardinality, $this->getFormDefaultByKey('date_membership_began')),
    ];

    return $form;
  }
}
