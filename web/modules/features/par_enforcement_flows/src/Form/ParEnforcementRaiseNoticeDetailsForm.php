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
class ParEnforcementRaiseNoticeDetailsForm extends ParBaseForm {

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

    // Get the correct authority id / organisation id set within the previous form.
    $enforcing_authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');
    $organisation_id = $this->getDefaultValues('par_data_organisation_id', '', 'par_enforce_organisation');

    // Load required entities for the enforcement flow.
    $par_data_organisation = ParDataOrganisation::load($organisation_id);
    $par_data_authority = ParDataAuthority::load($enforcing_authority_id);

    $form['authority'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['authority']['authority_heading']  = [
      '#type' => 'markup',
      '#markup' => $this->t('Notification of Enforcement action'),
    ];

    $form['authority']['authority_name'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<div><h1>',
      '#suffix' => '</h1></div>',
    ];

    $form['organisation'] =[
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['organisation']['organisation_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Regarding'),

    ];

    $form['organisation']['organisation_name'] = [
      '#type' => 'markup',
      '#markup' => $par_data_organisation->get('organisation_name')->getString(),
      '#prefix' => '<h1>',
      '#suffix' => '</h1>',
    ];

    // Display the primary address.
    $form['registered_address'] = $this->renderSection('Registered address', $par_data_organisation, ['field_premises' => 'summary'], [], FALSE, TRUE);

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

    $partnership = $this->getRouteParam('par_data_partnership');

    // Load the enforcing Authority.
    $acting_authority = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');

    if ($par_data_authority = ParDataAuthority::load($acting_authority)) {
      // Get logged in user ParDataPerson related to the primary authority.
      $authority_person = $this->getParDataManager()->getUserPerson($this->getCurrentUser(), $par_data_authority);
      $authority_person_id = $authority_person->id() ? $authority_person->id() : NULL;
    }

    $time = new \DateTime();

    $enforcementNotice_data = [
      'notice_type' => $this->getTempDataValue('enforcement_type'),
      'summary' => $this->getTempDataValue('action_summary'),
      'field_enforcing_authority' => $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection'),
      'field_organisation' => $this->getDefaultValues('par_data_organisation_id', '', 'par_enforce_organisation'),
      'field_person' =>  $authority_person_id,
      'field_partnership' => $partnership->id(),
      'notice_date' => $time->format("Y-m-d"),
    ];

    // Get the legal entity assigned from the previous form.
    $legal_entity_value = $this->getDefaultValues('legal_entities_select', '', 'par_enforcement_notice_raise');

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
      $form_state->setRedirect($this->getFlow()->getNextRoute('next'), ['par_data_partnership' => $partnership->id(), 'par_data_enforcement_notice' =>$enforcementAction->id()]);
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
