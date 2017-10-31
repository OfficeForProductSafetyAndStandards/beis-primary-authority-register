<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;
use Drupal\par_data\Entity\ParDataAuthority;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParEnforcementOfficerDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_officer_details';
  }

  /**
   * Title callback default.
   */
  public function titleCallback() {
    return;
  }


    /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPerson $authority_person
   *   The ParDataPerson being retrieved.
   */
  public function retrieveEditableValues(ParDataPerson $authority_person = NULL) {

    if ($authority_person) {
      // Load person data.
      $this->loadDataValue("first_name", $authority_person->get('first_name')->getString());
      $this->loadDataValue("last_name", $authority_person->get('last_name')->getString());
      $this->loadDataValue("work_phone", $authority_person->get('work_phone')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,ParDataPartnership $par_data_partnership = NULL) {

    // Load the Authority.
    $enforcing_authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');

    if (empty($enforcing_authority_id)) {
      $form['authority_enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You have not selected an authority to enforce on behalf of, please go back to the previous steps to complete this form.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
      // Defensive coding don't attempt to render form elements without having the appropriate object
      // $par_data_organisation and $par_data_authority proceeding without them will cause system failures.
      return parent::buildForm($form, $form_state);
    }

    if ($par_data_authority = ParDataAuthority::load($enforcing_authority_id)) {
      // Get logged in user ParDataPerson(s) related to the primary authority.
      $authority_person = $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
    }

    $this->retrieveEditableValues($authority_person);

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#default_value' => $this->getDefaultValues("first_name"),
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#default_value' => $this->getDefaultValues("last_name"),
    ];

    $form['work_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work phone'),
      '#default_value' => $this->getDefaultValues("work_phone"),
    ];

    $form['action_id'] = [
      '#type' => 'hidden',
      '#value' => $enforcing_authority_id,
    ];

    // Make sure to add the person cacheability data to this form.
    //$this->addCacheableDependency($authority_person);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
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

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Load the enforcing Authority.
    $enforcing_authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');

    if ($par_data_authority = ParDataAuthority::load($enforcing_authority_id)) {
      // Get logged in user ParDataPerson(s) related to the primary authority.
      $authority_person = $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
    }

    // Save person details.
    if ($authority_person) {
      $authority_person->set('first_name', $this->getTempDataValue('first_name'));
      $authority_person->set('last_name', $this->getTempDataValue('last_name'));
      $authority_person->set('work_phone', $this->getTempDataValue('work_phone'));

      if ($authority_person->save()) {
      }
      else {
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
