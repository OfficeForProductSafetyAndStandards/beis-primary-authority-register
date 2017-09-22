<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsApplicationOrganisationSearchForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_application_organisation_search';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    if ($this->getFlowName() === 'partnership_application') {
      return 'New Partnership Application';
    }

    return parent::titleCallback();
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

    $form['organisation_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organisation Name'),
      '#default_value' => $this->getDefaultValues('organisation_name'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('is_local_authority')) {
      $this->setElementError('organisation_name', $form_state, 'Please enter an organisation name.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
