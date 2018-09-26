<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "address_lookup",
 *   title = @Translation("Address lookup form.")
 * )
 */
class ParAddressLookupForm extends ParAddressForm {

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $plugin_class = (new \ReflectionClass($this))->getName();

    $form['lookup'] = [
      '#type' => 'textfield',
      '#title' => "Find address",
    ];

    // Attach the javascript for postcode lookup.
    $form['#attached']['library'][] = 'par_forms/par-postcode-lookup';
    $form['#attached']['drupalSettings']['par_forms']['address_lookup']['api_key'] = Settings::get('ideal_postcodes_api_key');

    // Retrieve the default form fields.
    $form['address'] = [
      '#type' => 'fieldset',
      '#title' => "Enter the address",
    ];
    $form = array_merge($form, parent::getElements($form, $cardinality));

    return $form;
  }
}
