<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataAuthority;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementRaiseNoticeDetailsForm extends ParBaseEnforcementForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_raise_details';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $this->retrieveEditableValues();
    $enforcement_notice_entity = $this->getParDataManager()->getParBundleEntity('par_data_enforcement_notice');

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

    $form['enforcement_title'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Include the following information'),
    ];

    $form['enforcement_text'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#prefix' => '<ul>',
      '#suffix' => '</ul>',
    ];

    $enforcement_data = [
      'Full details of the contravention',
      'Which products or services are affected',
      'Your proposed text for any statutory notice or draft changes etc',
      'Your reasons for proposing the enforcement action',
    ];

    foreach ($enforcement_data as $key => $value) {

      $form['enforcement_text']['body'][$key] = [
        '#type' => 'markup',
        '#markup' => $this->t($value),
        '#prefix' => '<li>',
        '#suffix' => '</li>',
      ];
    }

    $form['action_summary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Provide a summary of the enforcement notification'),
      '#default_value' => $this->getDefaultValues("action_summary"),
     ];

    $enforcement_notice_entity = $enforcement_notice_entity->getAllowedValues('notice_type');

    $form['enforcement_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enforcing type'),
      '#options' => $enforcement_notice_entity,
      '#default_value' => key($enforcement_notice_entity),
      '#required' => TRUE,
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);

    $enforcing_authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');
    $organisation_id = $this->getDefaultValues('par_data_organisation_id', '', 'par_enforce_organisation');

    if (empty($enforcing_authority_id)) {
      $this->setElementError('authority_enforcement_ids', $form_state, 'Please select an authority to enforce on behalf of to proceed.');
    }

    if (empty($organisation_id)) {
      $this->setElementError('organisation_enforcement_ids', $form_state, 'Please select an organisation to enforce on behalf of to proceed.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    if ($authority_person = $this->getEnforcingPerson()) {
      $enforcement_officer_id = $authority_person->id() ? $authority_person->id() : NULL;
    }

    if ($partnership = $this->getRouteParam('par_data_partnership')) {
      $partnership_id = $partnership->id() ? $partnership->id() : NULL;
    }

    $time = new \DateTime();

    $enforcementNotice_data = [
      'notice_type' => $this->getTempDataValue('enforcement_type'),
      'summary' => $this->getTempDataValue('action_summary'),
      'field_enforcing_authority' => $this->getEnforcingAuthorityID(),
      'field_organisation' => $this->getEnforcedOrganisationID(),
      'field_person' =>  $enforcement_officer_id,
      'field_partnership' => $partnership_id,
      'notice_date' => $time->format("Y-m-d"),
    ];

    // Get the legal entity assigned from the previous form.
    $legal_entity_value = $this->getEnforcedLegalEntity();

    // Check if we are using the legal entity text field instead of the entity ref field.
    if ($legal_entity_value == 'add_new') {
      $enforcementNotice_data['legal_entity_name'] = $this->getDefaultValues('alternative_legal_entity', '', 'par_enforcement_notice_raise');
    } else {
      // We are dealing with an entity id the storage will be set to an entity ref field.
      $enforcementNotice_data['field_legal_entity'] = $legal_entity_value;
    }

    $enforcementAction = \Drupal::entityManager()->getStorage('par_data_enforcement_notice')->create($enforcementNotice_data);

    if ($enforcementAction->save()) {
      $this->deleteStore();
      // Go directly to the action setup form we cannot use links within forms without losing form data.
      $form_state->setRedirect($this->getFlow()->getNextRoute('next'), ['par_data_partnership' => $partnership->id(), 'par_data_enforcement_notice' => $enforcementAction->id()]);
    }
    else {
      $message = $this->t('The enforcement entity %entity_id could not be saved for %form_id');
      $replacements = [
        '%entity_id' => $enforcementAction->id(),
        '%form_id' => $this->getFormId(),
     ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

}
