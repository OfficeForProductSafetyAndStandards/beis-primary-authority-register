<?php

namespace Drupal\par_flow_transition_business\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The primary contact form for the partnership details steps of the
 * 2nd Data Validation/Transition User Journey.
 */
class ParFlowTransitionAddressForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_business';

  public function getFormId() {
    return 'par_flow_transition_business_address';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param ParDataPremises $par_data_premises
   *   The PRemises being retrieved.
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
      $this->loadDataValue("address_{$par_data_premises->id()}_country_code", $address->get('country_code')->getString());
    }
    $this->loadDataValue('premises_id', $par_data_premises->id());
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_premises);

    $form['info'] = [
      '#markup' => t('Change the address of your business.'),
    ];

    // The Postcode.
    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_postal_code"),
      '#description' => t('Enter the postcode of the business'),
      '#required' => TRUE,
    ];

    // The Address lines.
    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 1'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_address_line1"),
      '#required' => TRUE,
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 2'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_address_line2"),
      '#required' => TRUE,
    ];

    $form['address_line3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('(NOT SAVED) Address Line 3'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_address_line3"),
    ];

    // Town/City.
    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Town / City'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_locality"),
      '#required' => TRUE,
    ];

    // County.
    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('County'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_administrative_area"),
      '#required' => TRUE,
    ];

    // Country.
    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_country_code"),
      '#required' => TRUE,
    ];

    // UPRN.
    $form['uprn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('(NOT SAVED) UPRN'),
      '#default_value' => $this->getDefaultValues("address_{$this->getDefaultValues('premises_id')}_uprn"),
      '#description' => t('The Unique Property Reference Number (UPRN) is a unique identification number for every address in Great Britain. If you know the UPRN , enter it here.')
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Next'),
    ];

    $previous_link = $this->getFlow()->getLinkByStep(4)->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('<br>%link', ['%link' => $previous_link]),
    ];

    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_premises);

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
      'country_code' => 'GB',
      'address_line1' => $this->getTempDataValue('address_line1'),
      'address_line2' => $this->getTempDataValue('address_line2'),
      'locality' => $this->getTempDataValue('town_city'),
      'administrative_area' => $this->getTempDataValue('county'),
      'postal_code' => $this->getTempDataValue('postcode'),
    ];

    $premises->set('address', $address);

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

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }
}
