<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_enforcement_raise_flows\ParFormCancelTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_enforcement_raise_flows\ParFlowAccessTrait;
use Drupal\user\Entity\User;

/**
 * The member contact form.
 */
class ParEnforcementNoticeDetailsForm extends ParBaseForm {

  use ParFlowAccessTrait;
  use ParFormCancelTrait;

  protected $formItems = [
    'par_data_enforcement_notice:enforcement_notice' => [
      'summary' => 'summary'
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    if ($par_data_partnership) {
      $cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_raise');
      $choosen_legal_entity = $this->getFlowDataHandler()->getDefaultValues('legal_entities_select', '', $cid);

      if ($choosen_legal_entity == 'add_new') {
        $enforced_entity_name = $this->getFlowDataHandler()->getDefaultValues('alternative_legal_entity', '', $cid);
      }
      else {
        $legal_entity = ParDataLegalEntity::load($choosen_legal_entity);
        $enforced_entity_name = $legal_entity ? $legal_entity->label() : '';
      }

      if (!empty($enforced_entity_name)) {
        $this->pageTitle = 'Proposed enforcement notification regarding | '. $enforced_entity_name;
      }
    }

    return parent::titleCallback();
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $authority_id = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', NULL, $cid);
    if ($par_data_authority = ParDataAuthority::load($authority_id)) {
      $account_id = $this->getFlowDataHandler()->getCurrentUser()->id();
      $account = User::load($account_id);

      // Get logged in user ParDataPerson(s) related to the primary authority.
      if ($par_data_person = $this->getParDataManager()->getUserPerson($account, $par_data_authority)) {
        // Set the person that's being edited.
        $this->getFlowDataHandler()->setParameter('par_data_person', $par_data_person);
      }
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $enforcement_notice_entity = $enforcement_notice_entity->getAllowedValues('notice_type');

    $form['enforcement_type'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('This enforcement action is'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['enforcement_type']['type'] = [
      '#type' => 'radios',
      '#options' => $enforcement_notice_entity,
      '#default_value' => key($enforcement_notice_entity),
      '#required' => TRUE,
    ];

    $form['summary_title'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Summary of enforcement notification'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['enforcement_title'] = [
      '#type' => 'fieldset',
      '#markup' => $this->t('Include the following information'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $enforcement_data = [
      'Full details of the contravention',
      'Which products or services are affected',
      'Your proposed text for any statutory notice or draft changes etc',
      'Your reasons for proposing the enforcement action',
    ];

    $form['enforcement_text'] = ['#theme' => 'item_list', '#items' => $enforcement_data];

    $form['action_summary_title'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Provide a summary of the enforcement notification'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
    ];

    $form['summary'] = [
      '#type' => 'textarea',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("summary"),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $cid = $this->getFlowNegotiator()->getFormKey('par_authority_selection');
    $enforcing_authority_id = $this->getFlowDataHandler()->getDefaultValues('par_data_authority_id', '', $cid);

    $cid = $this->getFlowNegotiator()->getFormKey('par_enforce_organisation');
    $organisation_id = $this->getFlowDataHandler()->getDefaultValues('par_data_organisation_id', '', $cid);

    if (empty($enforcing_authority_id)) {
      $this->setElementError('authority_enforcement_ids', $form_state, 'Please select an authority to enforce on behalf of to proceed.');
    }

    if (empty($organisation_id)) {
      $this->setElementError('organisation_enforcement_ids', $form_state, 'Please select an organisation to enforce on behalf of to proceed.');
    }
  }

}
