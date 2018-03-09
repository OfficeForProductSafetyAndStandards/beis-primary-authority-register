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
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_premises:premises' => [
      'address' => [
        'country_code' => 'country_code',
        'address_line1' => 'address_line1',
        'address_line2' => 'address_line2',
        'locality' => 'town_city',
        'postal_code' => 'postcode',
      ],
      'nation' => 'nation',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises')) {
      $address = $par_data_premises->get('address')->first();

      // Setting the address details..
      $this->getFlowDataHandler()->setFormPermValue("postcode", $address->get('postal_code')->getString());
      $this->getFlowDataHandler()->setFormPermValue("address_line1", $address->get('address_line1')->getString());
      $this->getFlowDataHandler()->setFormPermValue("address_line2", $address->get('address_line2')->getString());
      $this->getFlowDataHandler()->setFormPermValue("town_city", $address->get('locality')->getString());
      $this->getFlowDataHandler()->setFormPermValue("county", $address->get('administrative_area')->getString());
      $this->getFlowDataHandler()->setFormPermValue("nation", $par_data_premises->get('nation')->getString());
      $this->getFlowDataHandler()->setFormPermValue("country_code", $address->get('country_code')->getString());
      $this->getFlowDataHandler()->setFormPermValue("uprn", $par_data_premises->get('uprn')->getString());
      $this->getFlowDataHandler()->setFormPermValue('premises_id', $par_data_premises->id());
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
          'select[name="country_code"]' => [
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
