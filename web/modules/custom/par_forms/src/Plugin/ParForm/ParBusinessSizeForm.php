<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "business_size",
 *   title = @Translation("Number of members form.")
 * )
 */
class ParBusinessSizeForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['business_size', 'par_data_organisation', 'size', NULL, NULL, 0, [
      'You must fill in the missing information.' => 'You must enter how many members are coordinated by this business.'
    ]],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('business_size', $par_data_organisation->get('size')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $organisation_bundle = $this->getParDataManager()->getParBundleEntity('par_data_organisation');

    $form['info'] = [
      '#markup' => t('Enter the number of associations in your membership list'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    // Business details.
    $form['business_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of members'),
      '#default_value' => $this->getDefaultValuesByKey('business_size', $cardinality),
      '#options' => $organisation_bundle->getAllowedValues('size'),
    ];

    return $form;
  }
}
