<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParPartnershipFlowsApplicationAddOrganisationForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_organisation_add';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'New Partnership Application';
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $this->retrieveEditableValues();

    // Use ParDataPremises for allowed values.
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    // The Postcode.
    $form['address']['address_postal_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
      '#default_value' => $this->getDefaultValues("address_postal_code"),
      '#description' => t('Enter the postcode of the business'),
    ];

    // The Address lines.
    $form['address']['address_address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 1'),
      '#default_value' => $this->getDefaultValues("address_address_line1"),
    ];

    $form['address']['address_address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line 2'),
      '#default_value' => $this->getDefaultValues("address_address_line2"),
    ];

    // Town/City.
    $form['address']['address_locality'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Town / City'),
      '#default_value' => $this->getDefaultValues("address_locality"),
    ];

    // County.
    $form['address']['address_administrative_area'] = [
      '#type' => 'textfield',
      '#title' => $this->t('County'),
      '#default_value' => $this->getDefaultValues("address_administrative_area"),
    ];

    // Country.
    $form['address']['address_country_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getDefaultValues("address_country_code"),
    ];

    // UPRN.
    $form['address']['address_uprn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('UPRN'),
      '#default_value' => $this->getDefaultValues("address_uprn"),
      '#description' => t('The Unique Property Reference Number (UPRN) is a unique identification number for every address in Great Britain. If you know the UPRN , enter it here.'),
    ];

    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => t('Continue'),
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#name' => 'cancel',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancelForm'],
      '#attributes' => [
        'class' => ['btn-link']
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

}
