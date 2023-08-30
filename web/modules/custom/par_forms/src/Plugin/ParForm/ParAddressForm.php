<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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
  protected array $entityMapping = [
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
  public function loadData(int $index = 1): void {
    if ($par_data_premises = $this->getFlowDataHandler()->getParameter('par_data_premises')) {
      $address = $par_data_premises->get('address')->first();

      // Setting the address details..
      $this->setDefaultValuesByKey("postcode", $index, $address->get('postal_code')->getString());
      $this->setDefaultValuesByKey("address_line1", $index, $address->get('address_line1')->getString());
      $this->setDefaultValuesByKey("address_line2", $index, $address->get('address_line2')->getString());
      $this->setDefaultValuesByKey("town_city", $index, $address->get('locality')->getString());
      $this->setDefaultValuesByKey("county", $index, $address->get('administrative_area')->getString());
      $this->setDefaultValuesByKey("nation", $index, $par_data_premises->get('nation')->getString());
      $this->setDefaultValuesByKey("country_code", $index, $address->get('country_code')->getString());
      $this->setDefaultValuesByKey("uprn", $index, $par_data_premises->get('uprn')->getString());
      $this->setDefaultValuesByKey('premises_id', $index, $par_data_premises->id());
    }

    parent::loadData($index);
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
  public function getElements(array $form = [], int $index = 1) {
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['premises_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getFlowDataHandler()->getDefaultValues('premises_id', 'new'),
    ];

    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 1'),
      '#default_value' => $this->getDefaultValuesByKey('address_line1', $index),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Address Line 2'),
      '#default_value' => $this->getDefaultValuesByKey('address_line2', $index),
    ];

    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Town / City'),
      '#default_value' => $this->getDefaultValuesByKey('town_city', $index),
    ];

    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter County'),
      '#default_value' => $this->getDefaultValuesByKey('county', $index),
    ];

    $form['country_code'] = [
      '#type' => 'select',
      '#options' => $this->getCountryRepository()->getList(NULL),
      '#title' => $this->t('Country'),
      '#default_value' => $this->getDefaultValuesByKey('country_code', $index, 'GB'),
    ];

    $form['nation'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your Nation'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getDefaultValuesByKey('nation', $index),
      '#states' => [
        'visible' => [
          'select[name="' . $this->getTargetName($this->getElementKey('country_code', $index)) . '"]' => [
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
