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
class ParPartnershipFlowsApplicationOrganisationForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['organisation_name', 'par_data_organisation', 'organisation_name', NULL, NULL, 0, [
      'You must fill in the missing information.' => "You must enter the organisation's name."
    ]],
  ];

  protected $pageTitle = 'Who are you in partnership with?';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['organisation_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provide the business or organisation name'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('organisation_name'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
