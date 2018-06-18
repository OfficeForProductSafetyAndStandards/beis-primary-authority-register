<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "organisation_name",
 *   title = @Translation("Organisation name form.")
 * )
 */
class ParOrganisationNameForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'organisation_name' => 'name',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('name', $par_data_organisation->get('organisation_name')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the business or organisation name'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('name'),
    ];

    return $form;
  }
}
