<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the premises details.
 */
class ParPartnershipFlowsAddressForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  protected $formItems = [];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_address';
  }

  /**
   * Helper to get all the editable values.
   *
   * Used for when editing or revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataPremises $par_data_premises
   *   The Premises being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }

    $address = $par_data_premises->get('address')[0];

    if ($par_data_premises) {
      // Address.
      $this->loadDataValue("address_{$par_data_premises->id()}_postal_code", $address->get('postal_code')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_address_line1", $address->get('address_line1')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_address_line2", $address->get('address_line2')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_locality", $address->get('locality')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_administrative_area", $address->get('administrative_area')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_country_code", $par_data_premises->get('nation')->getString());
      $this->loadDataValue("address_{$par_data_premises->id()}_uprn", $par_data_premises->get('uprn')->getString());
      $this->loadDataValue('premises_id', $par_data_premises->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {

    $bundle = $par_data_partnership->bundle();
    // Can't add administrative_area as part of the validation as it needs to
    // be blank and we need it to have data in it.
    $this->formItems = [
      "par_data_premises:$bundle" => [
        'address' => [
          'country_code' => 'country_code',
          'address_line1' => 'address_line1',
          'address_line2' => 'address_line2',
          'locality' => 'town_city',
          'postal_code' => 'postcode',
        ],
        'nation' => 'country',
      ],
    ];

    $this->retrieveEditableValues($par_data_partnership, $par_data_premises);
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['info'] = [
      '#markup' => t('Edit your registered address'),
      '#prefix' => '<h2>',
      '#suffix' => '</h2>',
    ];

    // The Address lines.
    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 1'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_address_line1"),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 2'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_address_line2"),
    ];

    // Town/City.
    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Town / City'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_locality"),
    ];

    // County.
    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('County'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_administrative_area"),
    ];

    // Country.
    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_country_code"),
    ];

    // The Postcode.
    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_postal_code"),
    ];

    // The Postcode.
    $form['country_code'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Country'),
      '#default_value' => 'GB',
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Save'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_premises);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $premises = $this->getRouteParam('par_data_premises');
    $address = [
      'country_code' => $this->getTempDataValue('country_code'),
      'address_line1' => $this->getTempDataValue('address_line1'),
      'address_line2' => $this->getTempDataValue('address_line2'),
      'locality' => $this->getTempDataValue('town_city'),
      'administrative_area' => $this->getTempDataValue('county'),
      'postal_code' => $this->getTempDataValue('postcode'),
    ];

    $premises->set('address', $address);
    $premises->set('nation', $this->getTempDataValue('country'));

    if ($premises->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %premises could not be saved for %form_id');
      $replacements = [
        '%premises' => $premises->get('address')->toString(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
