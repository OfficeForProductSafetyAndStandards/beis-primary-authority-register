<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "address",
 *   title = @Translation("Address form.")
 * )
 */
class ParAddressForm extends ParFormPluginBase {

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['country_code', 'par_data_premises', 'address', 'country_code', NULL, 0, []],
    ['address_line1', 'par_data_premises', 'address', 'address_line1', NULL, 0, [
      'Street address field is required.' => 'You must enter the first line of your address',
    ]],
    ['address_line2', 'par_data_premises', 'address', 'address_line2', NULL, 0, []],
    ['town_city', 'par_data_premises', 'address', 'locality', NULL, 0, [
      'Post town field is required.' => 'You must enter the town or city for this address'
    ]],
    ['postcode', 'par_data_premises', 'address', 'postal_code', NULL, 0, [
      'Postal code field is required.' => 'You must enter a valid postcode.'
    ]],
    ['nation', 'par_data_premises', 'nation', NULL, NULL, 0, []],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises')) {
      $address = $par_data_premises->get('address')->first();

      // Setting the address details..
      $this->setDefaultValuesByKey("postcode", $cardinality, $address->get('postal_code')->getString());
      $this->setDefaultValuesByKey("address_line1", $cardinality, $address->get('address_line1')->getString());
      $this->setDefaultValuesByKey("address_line2", $cardinality, $address->get('address_line2')->getString());
      $this->setDefaultValuesByKey("town_city", $cardinality, $address->get('locality')->getString());
      $this->setDefaultValuesByKey("county", $cardinality, $address->get('administrative_area')->getString());
      $this->setDefaultValuesByKey("nation", $cardinality, $par_data_premises->get('nation')->getString());
      $this->setDefaultValuesByKey("country_code", $cardinality, $address->get('country_code')->getString());
      $this->setDefaultValuesByKey("uprn", $cardinality, $par_data_premises->get('uprn')->getString());
      $this->setDefaultValuesByKey('premises_id', $cardinality, $par_data_premises->id());
    }

    parent::loadData();
  }

  /**
   * Get the country repository from the address module.
   */
  public function getCountryRepository() {
    return \Drupal::service('address.country_repository');
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['premises_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getFlowDataHandler()->getDefaultValues('premises_id', 'new'),
    ];

    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 1'),
      '#default_value' => $this->getDefaultValuesByKey('address_line1', $cardinality),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 2'),
      '#default_value' => $this->getDefaultValuesByKey('address_line2', $cardinality),
    ];

    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Town / City'),
      '#default_value' => $this->getDefaultValuesByKey('town_city', $cardinality),
    ];

    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter County'),
      '#default_value' => $this->getDefaultValuesByKey('county', $cardinality),
    ];

    $form['country_code'] = [
      '#type' => 'select',
      '#options' => $this->getCountryRepository()->getList(NULL),
      '#title' => $this->t('Country'),
      '#default_value' => $this->getDefaultValuesByKey('country_code', $cardinality, 'GB'),
    ];

    $form['nation'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your Nation'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getDefaultValuesByKey('nation', $cardinality),
      '#states' => [
        'visible' => [
          'select[name="' . $this->getTargetName($this->getElementKey('country_code', $cardinality)) . '"]' => [
            ['value' => 'GB'],
          ],
        ],
      ],
    ];

    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Postcode'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("postcode"),
    ];

    return $form;
  }
}
