<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\Entity\ParDataSicCode;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The partnership form for the partnership details.
 */
class ParConfirmationReviewForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_review';
  }

  /**
   * {@inheritdoc}
   */
  protected $pageTitle = 'Review the partnership summary information below';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $form['partnership_id'] = [
      '#type' => 'hidden',
      '#value' => $par_data_partnership->id(),
    ];

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());

    // Display details about the organisation for information.
    $form['about_organisation'] = $this->renderSection('About the organisation', $par_data_organisation, ['comments' => 'about']);

    // Display organisation name and organisation primary address.
    $form['organisation_name'] = $this->renderSection('Business name', $par_data_organisation, ['organisation_name' => 'title'], [], TRUE, TRUE);
    $form['organisation_registered_address'] = $this->renderSection('Business address', $par_data_organisation, ['field_premises' => 'summary'], [], TRUE, TRUE);

    // Display contacts at the organisation.
    $form['organisation_contacts'] = $this->renderSection('Contacts at the Organisation', $par_data_partnership, ['field_organisation_person' => 'detailed'], [],  TRUE, TRUE);

    // Display SIC code, number of employees.
    $form['sic_code'] = $this->renderSection('Primary SIC code', $par_data_organisation, ['field_sic_code' => 'detailed'], [], TRUE, TRUE);
    $form['number_employees'] = $this->renderSection('Number of employees at the organisation', $par_data_organisation, ['employees_band' => 'detailed']);

    // Display legal entities.
    $form['legal_entities'] = $this->renderSection('Legal entities', $par_data_organisation, ['field_legal_entity' => 'detailed']);

    $form['partnership_info_agreed_business'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm I have reviewed the information above'),
      '#disabled' => $par_data_partnership->get('partnership_info_agreed_business')->getString(),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("partnership_info_agreed_business"),
      '#return_value' => 'on',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Make sure the confirm box is ticked.
    if (!$form_state->getValue('partnership_info_agreed_business')) {
      $this->setElementError('partnership_info_agreed_business', $form_state, 'Please confirm you have reviewed the details.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $par_data_partnership->getOrganisation(TRUE);
    if (empty($par_data_organisation)) {
      $par_data_organisation = ParDataOrganisation::create();
    }

    // Set the data for the about form.
    $par_data_organisation->set('comments', $this->getFlowDataHandler()->getTempDataValue('about_business', 'par_partnership_confirmation_about_business'));

    // Set the data for the address form.
    $par_data_premises = $par_data_organisation->getPremises(TRUE);
    if (empty($par_data_premises)) {
      $par_data_premises = ParDataPremises::create();
    }

    $address = [
      'country_code' => $this->getFlowDataHandler()->getTempDataValue('country_code', 'par_partnership_confirmation_address'),
      'address_line1' => $this->getFlowDataHandler()->getTempDataValue('address_line1', 'par_partnership_confirmation_address'),
      'address_line2' => $this->getFlowDataHandler()->getTempDataValue('address_line2', 'par_partnership_confirmation_address'),
      'locality' => $this->getFlowDataHandler()->getTempDataValue('town_city', 'par_partnership_confirmation_address'),
      'administrative_area' => $this->getFlowDataHandler()->getTempDataValue('county', 'par_partnership_confirmation_address'),
      'postal_code' => $this->getFlowDataHandler()->getTempDataValue('postcode', 'par_partnership_confirmation_address'),
    ];
    $par_data_premises->set('address', $address);
    $par_data_premises->set('nation', $this->getFlowDataHandler()->getTempDataValue('country', 'par_partnership_confirmation_address'));

    // Set the data for the contact form.
    $par_data_person = $par_data_partnership->getOrganisationPeople(TRUE);
    if (empty($par_data_person)) {
      $par_data_person = ParDataPremises::create();
    }

    $par_data_person->set('salutation', $this->getFlowDataHandler()->getTempDataValue('salutation', 'par_partnership_confirmation_contact'));
    $par_data_person->set('first_name', $this->getFlowDataHandler()->getTempDataValue('first_name', 'par_partnership_confirmation_contact'));
    $par_data_person->set('last_name', $this->getFlowDataHandler()->getTempDataValue('last_name', 'par_partnership_confirmation_contact'));
    $par_data_person->set('work_phone', $this->getFlowDataHandler()->getTempDataValue('work_phone', 'par_partnership_confirmation_contact'));
    $par_data_person->set('mobile_phone', $this->getFlowDataHandler()->getTempDataValue('mobile_phone', 'par_partnership_confirmation_contact'));
    $par_data_person->set('email', $this->getFlowDataHandler()->getTempDataValue('email', 'par_partnership_confirmation_contact'));
    $par_data_person->set('communication_notes', $this->getFlowDataHandler()->getTempDataValue('notes', 'par_partnership_confirmation_contact'));
    $email_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_email'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_email']);
    $par_data_person->set('communication_email', $email_preference_value);
    $work_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_phone'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_phone']);
    $par_data_person->set('communication_phone', $work_phone_preference_value);
    $mobile_phone_preference_value = isset($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_mobile'])
      && !empty($this->getFlowDataHandler()->getTempDataValue('preferred_contact', 'par_partnership_confirmation_contact')['communication_mobile']);
    $par_data_person->set('communication_mobile', $mobile_phone_preference_value);

    // Save the data for the SIC code form.
    $par_data_organisation->get('field_sic_code')->set(0, $this->getFlowDataHandler()->getTempDataValue('sic_code', 'par_partnership_confirmation_sic_code'));

    // Save the data for the business size form.
    $par_data_organisation->set('employees_band', $this->getFlowDataHandler()->getTempDataValue('employees_band', 'par_partnership_confirmation_business_size'));

    // Save the data for the trading name form.
    $par_data_organisation->set('trading_name', $this->getFlowDataHandler()->getTempDataValue('trading_name', 'par_partnership_confirmation_trading_name'));

    // Add all references if not already set.
    if ($par_data_person->save() && !$par_data_partnership->getOrganisation(TRUE)) {
      $par_data_partnership->get('field_organisation_person')->set(0, $par_data_person);
      $par_data_organisation->get('field_person')->appendItem($par_data_person);
    }
    if ($par_data_premises->save() && !$par_data_organisation->getPremises(TRUE)) {
      $par_data_organisation->get('field_premises')->set(0, $par_data_premises);
    }
    if ($par_data_organisation->save() && !$par_data_partnership->getOrganisation(TRUE)) {
      $par_data_partnership->get('field_organisation')->set(0, $par_data_organisation);
    }

    // Set the confirmed by business field.
    if ($par_data_partnership && !$par_data_partnership->getBoolean('partnership_info_agreed_business')) {
      // Save the value for the confirmation field.
      $par_data_partnership->set('partnership_info_agreed_business', $this->decideBooleanValue($this->getFlowDataHandler()->getTempDataValue('partnership_info_agreed_business')));

      // Set partnership status.
      $par_data_partnership->setParStatus('confirmed_business');
    }

    // Save the partnership.
    if ($par_data_partnership->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('This %confirm could not be saved for %form_id');
      $replacements = [
        '%confirm' => $par_data_partnership->get('partnership_info_agreed_business')->toString(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);

      // If the partnership could not be saved the application can't be progressed.
      // @TODO Find a better way to alert the user without redirecting them away from the form.
      drupal_set_message('There was an error progressing your partnership, please contact the helpdesk for more information.');
      $form_state->setRedirect($this->getFlowNegotiator()->getFlow()->getPrevRoute('cancel'));
    }

  }

}
