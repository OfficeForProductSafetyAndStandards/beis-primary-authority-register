<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

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
    $enforcement_notice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_enforcement_notice');

    //get the correct par_data_authority_id set by the previous form
    $authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_authority = current($par_data_partnership->getAuthority());

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

    $form['enforcement_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Enforcing type'),
      '#options' => $enforcement_notice_bundle->getAllowedValues('notice_type'),
      '#default_value' => $this->getDefaultValues('enforcement_type'),
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $partnership = $this->getRouteParam('par_data_partnership');
    $enforcementNotice_data = [];

    $enforcementNotice_data = [
      'notice_type' => $this->getTempDataValue('enforcement_type'),
      'summary' => $this->getTempDataValue('action_summary'),
      'field_regulatory_function' => $this->getTempDataValue('regulatory_functions'),
      'field_enforcing_authority' => $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection'),
      'field_organisation' => $this->getDefaultValues('par_data_organisation_id', '', 'par_data_organisation_id'),
      'field_partnership' => $partnership->id(),
    ];

    //get the legal entity assigned from the previous form.
    $legal_entity_value = $this->getDefaultValues('legal_entities_select', '', 'par_enforcement_notice_raise');

    //check if we are using the legal entity text field instead of the entity ref field.
    if ($legal_entity_value == 'add_new') {
      $enforcementNotice_data['legal_entity_name'] = $this->getDefaultValues('alternative_legal_entity', '', 'par_enforcement_notice_raise');
    } else {
      //we are dealing with an entity id the storage will be set to an entity ref field.
      $enforcementNotice_data['field_legal_entity'] = $legal_entity_value;
    }

    $enforcementAction = \Drupal::entityManager()->getStorage('par_data_enforcement_notice')->create($enforcementNotice_data);

    if ($enforcementAction->save()) {
      $this->deleteStore();
      //Go directly to the action setup form we cannot use links within forms without losing form data.
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
