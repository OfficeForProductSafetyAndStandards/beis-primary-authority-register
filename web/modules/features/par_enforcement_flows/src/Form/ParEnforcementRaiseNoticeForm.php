<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowException;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementRaiseNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_raise';
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

    // Get the correct par_data_authority_id set by the previous form.
    $enforcing_authority_id = $this->getDefaultValues('par_data_authority_id', '', 'par_authority_selection');
    $organisation_id = $this->getDefaultValues('par_data_organisation_id', '', 'par_enforce_organisation');

    if (empty($enforcing_authority_id)) {
      $form['authority_enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You have not selected an authority to enforce on behalf of, please go back to the previous steps to complete this form.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
    }

    if (empty($organisation_id)) {
      $form['organisation_enforcement_ids'] = [
        '#type' => 'markup',
        '#markup' => $this->t('You have not selected an organisation to enforce on behalf of, please go back to the previous steps to complete this form.'),
        '#prefix' => '<p><strong>',
        '#suffix' => '</strong><p>',
      ];
    }

    // Defensive coding don't attempt to render form elements without having the appropriate object
    // $par_data_organisation and $par_data_authority proceeding without them will cause system failures.
    if (empty($organisation_id) || empty($enforcing_authority_id)) {
      return parent::buildForm($form, $form_state);
    }

    // Organisation summary.
    $par_data_organisation = ParDataOrganisation::load($organisation_id);
    $par_data_authority = current($par_data_partnership->getAuthority());

    $form['authority'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['authority']['authority_heading'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Notification of Enforcement action'),
    ];


    $form['authority']['authority_name'] = [
      '#type' => 'markup',
      '#markup' => $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<div><h1>',
      '#suffix' => '</h1></div>',
    ];

    $form['organisation'] = [
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

    $legal_entity_reg_names = $par_data_organisation->getPartnershipLegalEntities();
    // After getting a list of all the associated legal entities add a use
    // custom option.
    $legal_entity_reg_names['add_new'] = 'Add a legal entity';

    // Choose the defaults based on how many legal entities there are to choose
    // from.
    if (count($legal_entity_reg_names) >= 1 && !$this->getDefaultValues('legal_entities_select', FALSE)) {
      $default = key($legal_entity_reg_names);
    }
    elseif (count($legal_entity_reg_names) < 1 && !$this->getDefaultValues('legal_entities_select', FALSE)) {
      $default = 'add_new';
    }

    $form['legal_entities_select'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select a legal entity'),
      '#options' => $legal_entity_reg_names,
      '#default_value' => $this->getDefaultValues('legal_entities_select', $default),
      '#required' => TRUE,
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $form['alternative_legal_entity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the name of the legal entity'),
      '#default_value' => $this->getDefaultValues("alternative_legal_entity"),
      '#states' => array(
        'visible' => array(
          ':input[name="legal_entities_select"]' => array('value' => 'add_new'),
        ),
      ),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
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

}
