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
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_coordinated_business:coordinated_business' => [
      'date_membership_ceased' => 'date_membership_ceased',
    ],
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

  /**
   * Validate date field.
   */
  public function validateForm(&$form_state, $cardinality = 1) {
    parent::validate($form_state, $cardinality);
  }
}
