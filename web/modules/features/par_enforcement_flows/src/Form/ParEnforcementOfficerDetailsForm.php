<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\Core\Session\AccountProxyInterface;
/**
 * Enforcement officer details form used to update/store new details
 * for the enforcing officer during the raise enforcement flow.
 */
class ParEnforcementOfficerDetailsForm extends ParBaseEnforcementForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_officer_details';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {

    $enforcementFlowTitle = $this->RaiseEnforcementTitleCallback();
    if ($enforcementFlowTitle) {
      $this->pageTitle =  $enforcementFlowTitle;
    }
    return parent::titleCallback();
  }

    /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPerson $authority_person
   *   The ParDataPerson being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership) {

    $this->setState("edit:{$par_data_partnership->id()}");

    if ($enforcement_officer = $this->getEnforcingPerson()) {
      // Load person data.
      $this->loadDataValue("first_name", $enforcement_officer->get('first_name')->getString());
      $this->loadDataValue("last_name", $enforcement_officer->get('last_name')->getString());
      $this->loadDataValue("work_phone", $enforcement_officer->get('work_phone')->getString());
      $this->loadDataValue("enforcement_officer_id", $enforcement_officer->id());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,ParDataPartnership $par_data_partnership = NULL) {

    $this->retrieveEditableValues($par_data_partnership);
    // Ensure we have all the required enforcement data stored in the cache in order to proceed.
    $cached_enforcement_data = $this->validateEnforcementCachedData();

    if ($cached_enforcement_data === TRUE){
      $raise_enforcement_form = $this->BuildRaiseEnforcementFormElements();
      $form = array_merge($form, $raise_enforcement_form);
    }
    else {
      $form = array_merge($form, $cached_enforcement_data);
      return parent::buildForm($form, $form_state);
    }

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Please confirm your first name'),
      '#default_value' => $this->getDefaultValues("first_name"),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Please confirm your last name'),
      '#default_value' => $this->getDefaultValues("last_name"),
    ];

    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Please confirm your work phone'),
      '#default_value' => $this->getDefaultValues("work_phone"),
    ];

    $form['enforcement_officer_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getDefaultValues('enforcement_officer_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $enforcing_authority_id = $this->getEnforcingAuthorityID();
    $enforced_organisation_id = $this->getEnforcedOrganisationID();
    $cached_data_available = TRUE;

    if (empty($enforcing_authority_id)) {
      $this->setElementError('authority_enforcement_ids', $form_state, 'Please select an authority to enforce on behalf of to proceed.');
      $cached_data_available = FALSE;
    }

    if (empty($enforced_organisation_id)) {
      $this->setElementError('organisation_enforcement_ids', $form_state, 'Please select an organisation to enforce on behalf of to proceed.');
      $cached_data_available = FALSE;
    }
    // Only validate the form fields if they have been rendered.
    if ($cached_data_available === TRUE) {
      // Validate required fields.
      if (empty($form_state->getValue('first_name'))) {
        $form_state->setErrorByName('first_name', $this->t('<a href="#edit-first-name">The first name field is required.</a>'));
      }

      if (empty($form_state->getValue('last_name'))) {
        $form_state->setErrorByName('last_name', $this->t('<a href="#edit-last-name">The last name field is required.</a>'));
      }

      if (empty($form_state->getValue('work_phone'))) {
        $form_state->setErrorByName('work_phone', $this->t('<a href="#edit-work-phone">The work phone field is required.</a>'));
      }
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $enforcement_officer = $this->getEnforcingOfficerEntity();

    // Save updated par person details.
    if ($enforcement_officer) {
      $enforcement_officer->set('first_name', $this->getTempDataValue('first_name'));
      $enforcement_officer->set('last_name', $this->getTempDataValue('last_name'));
      $enforcement_officer->set('work_phone', $this->getTempDataValue('work_phone'));

      if (!$enforcement_officer->save()) {
        $message = $this->t('This %person could not be updated/saved for %form_id');
        $replacements = [
          '%person' => $this->getTempDataValue('first_name'),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }
    }
  }

}
