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

    $enforcement_data = array('Full details of the contravention', 'Which products or services are affected', 'Your proposed text for any statutory notice or draft changes etc', 'Your reasons for proposing the enforcement action');

    foreach ($enforcement_data as $key => $value) {

      $form['enforcement_text']['body'][$key] = [
        '#type' => 'markup',
        '#markup' => $this->t($value),
        '#prefix' => '<li>',
        '#suffix' => '</li>',
      ];
    }

    $form['email_copy'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Copy in another enforcing officer(optional)'),
    ];


    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#default_value' => $this->getDefaultValues("email"),
    ];


    $legal_entity_link = $this->getFlow()->getPrevLink('select_legal_form')->setText('Select legal entities')->toString();
    $form['legal_select_link'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $legal_entity_link]),
      '#prefix' => '<div>',
      '#suffix' => '</div></br>',
    ];

    $form['action_summmary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your summary of enforcement action(s)'),
      '#default_value' => $this->getDefaultValues("action_summmary_data"),
     ];


    $form['premises_address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address of premises concerned (if applicable)'),
      '#default_value' => $this->getDefaultValues("legal_entity_registered_name"),
    ];

    $add_action_link = $this->getFlow()->getNextLink()->setText('Add an enforcement action')->toString();
    $form['add'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $add_action_link]),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => $this->t('Next'),
    ];

    $cancel_link = $this->getFlow()->getPrevLink('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
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
