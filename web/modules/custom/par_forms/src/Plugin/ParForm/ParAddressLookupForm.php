<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\Core\Form\FormStateInterface;
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

    // Add a new action for the postcode lookup.
//    $this->getFlowNegotiator()->getFlow()->disableAction('next');
//    $this->getFlowNegotiator()->getFlow()->disableAction('done');
//    $this->getFlowNegotiator()->getFlow()->disableAction('save');
//    $form['postcode_lookup'] = [
//      '#type' => 'submit',
//      '#name' => "postcode-lookup:{$this->getPluginId()}:{$cardinality}",
//      '#weight' => 100,
//      '#submit' => ["$plugin_class::postcodeLookup"],
//      '#value' => $this->t("Find address"),
//      '#attributes' => [
//        'class' => ['cta-submit'],
//      ],
//    ];

    $form['address'] = [
      '#type' => 'fieldset',
      '#title' => "Enter the address",
    ];
    $form = array_merge($form, parent::getElements($form, $cardinality));

    return $form;
  }
}
