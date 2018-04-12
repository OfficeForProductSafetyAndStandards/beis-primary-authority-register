<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About business form plugin.
 *
 * @ParForm(
 *   id = "enorcement_notice",
 *   title = @Translation("Enforcement notice form.")
 * )
 */
class ParOrganisationNameForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_organisation:organisation' => [
      'organisation_name' => 'name',
    ],
  ];

  /**
   * Load the data for this form.
   */
  public function loadData($cardinality = 1) {
    if ($par_data_organisation = $this->getFlowDataHandler()->getParameter('par_data_organisation')) {
      $this->getFlowDataHandler()->setFormPermValue('name', $par_data_organisation->get('organisation_name')->getString());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {

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
    if (count($legal_entity_reg_names) >= 1 && !$this->getFlowDataHandler()->getDefaultValues('legal_entities_select', FALSE)) {
      $default = key($legal_entity_reg_names);
    }
    elseif (count($legal_entity_reg_names) < 1 && !$this->getFlowDataHandler()->getDefaultValues('legal_entities_select', FALSE)) {
      $default = 'add_new';
    }

    $form['legal_entities_select'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select a legal entity'),
      '#options' => $legal_entity_reg_names,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValuesByKey('legal_entities_select', $cardinality, $default),
      '#required' => TRUE,
      '#prefix' => '<div>',
      '#suffix' => '</div>',
    ];

    $form['alternative_legal_entity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the name of the legal entity'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValuesByKey("alternative_legal_entity", $cardinality),
      '#states' => array(
        'visible' => array(
          ':input[name="legal_entities_select"]' => array('value' => 'add_new'),
        ),
      ),
    ];

    return $form;
  }
}
