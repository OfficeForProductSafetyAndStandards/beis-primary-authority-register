<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementReferredAuthorityForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_referred_authority';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    if ($par_data_enforcement_notice) {
      $this->setState("approve:{$par_data_enforcement_notice->id()}");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    $this->retrieveEditableValues($par_data_enforcement_notice);
    $authorities = $this->get_all_authorities_for_organisation($par_data_enforcement_notice);
    $referrals = FALSE;

    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {

      $form_data = $this->getTempDataValue(['actions', $delta], 'par_enforcement_notice_approve');

      if ($form_data['primary_authority_status'] === ParDataEnforcementAction::REFERRED) {
        $referrals = TRUE;

        // It is only possibly to refer
        if (count($authorities) >= 0) {

          $form['referred_to'][$delta] = [
            '#type' => 'fieldset',
            '#collapsible' => FALSE,
            '#collapsed' => FALSE,
          ];

          $form['referred_to'][$delta]['titles'][$action->id()]= $this->renderSection('Title of action', $action, ['title' => 'title']);

          $form['referred_to'][$delta]['referrals'][$action->id()] = [
            '#type' => 'radios',
            '#title' => $this->t('Choose an authority to refer to'),
            '#options' => $authorities,
            '#default_value' => $this->getDefaultValues(['referred_to', $delta, $action->id()], 'par_enforcement_referred_authority'),
            '#required' => TRUE,
          ];
        }
        else {
          $form['help_text'] = [
            '#type' => 'markup',
            '#attributes' => ['class' => 'form-group'],
            '#markup' => 'There are no authorities that this action can be referred to, please return to the previous step and change your review.',
          ];
          // If no authorities are found on this partnership we should not continue looping through actions.
          return parent::buildForm($form, $form_state);
        }
      }
    }

    // Redirect to the next page if there are no referrals.
    if (!$referrals) {
      return $this->redirect($this->getFlow()->getNextRoute('next'), $this->getRouteParams());
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  public function get_all_authorities_for_organisation(ParDataEnforcementNotice $par_data_enforcement_notice) {

    // Discover all authorities that are responsible for
    // partnerships with this organisation.
    // @TODO Implement a better method to do this.
    $par_data_partnership = current($par_data_enforcement_notice->get('field_partnership')->referencedEntities());
    $par_data_organisation = current($par_data_partnership->get('field_organisation')->referencedEntities());

    // Get all partnerships with the same organisation,
    // that aren't deleted and were transitioned.
    $conditions = [
      'name' => [
        'AND' => [
          ['field_organisation', $par_data_organisation->id()],
        ]
      ],
    ];

    $organisation_partnerships = $this->getParDataManager()
      ->getEntitiesByQuery('par_data_partnership', $conditions, 10);

    // Load all the authorities belonging to these partnerships.
    $authorities = [];
    foreach ($organisation_partnerships as $partnership) {
      $authority = current($partnership->get('field_authority')->referencedEntities());

      if ($partnership->isLiving() && $authority->isLiving()) {
        $authorities[$authority->id()] = $authority->label();
      }
    }

    return $authorities;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }
}
