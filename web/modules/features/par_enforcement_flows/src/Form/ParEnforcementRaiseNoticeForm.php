<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The raise form for creating a new enforcement notice.
 */
class ParEnforcementRaiseNoticeForm extends ParBaseEnforcementForm {

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
   * {@inheritdoc}
   */
  public function titleCallback() {

    $enforcementFlowTitle = $this->RaiseEnforcementTitleCallback();
    if ($enforcementFlowTitle) {
      $this->pageTitle = $enforcementFlowTitle;
    }
    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    $this->setState("edit:{$par_data_partnership->id()}");
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $default = NULL;
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


    $legal_entity_reg_names = $this->getEnforcedOrganisationLegalEntities();
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

    $enforcing_authority_id = $this->getEnforcingAuthorityID();
    $enforced_organisation_id = $this->getEnforcedOrganisationID();

    if (empty($enforcing_authority_id)) {
      $this->setElementError('authority_enforcement_ids', $form_state, 'Please select an authority to enforce on behalf of to proceed.');
    }

    if (empty($enforced_organisation_id)) {
      $this->setElementError('organisation_enforcement_ids', $form_state, 'Please select an organisation to enforce on behalf of to proceed.');
    }
    parent::validateForm($form, $form_state);
  }

}
