<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementConfirmNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_approve_confirm';
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
    $this->retrieveEditableValues();

    $form['authority'] = $this->renderSection('Notification of Enforcement action from', $par_data_enforcement_notice, ['field_enforcing_authority' => 'title']);

    if (!$par_data_enforcement_notice->get('field_legal_entity')->isEmpty()) {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['field_legal_entity' => 'title']);

      // @TODO If there is only one organisation for this legal entity
      // we can potentially display the address, but otherwise we
      // can only display the name.
    }
    else {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['legal_entity_name' => 'summary']);
    }

    // Show details of each action.
    if (!$par_data_enforcement_notice->get('field_enforcement_action')->isEmpty()) {
      $form['actions'] = [
        '#type' => 'fieldset',
        '#tree' => TRUE,
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
    }
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form['actions'][$delta] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['actions'][$delta]['title'] = $this->renderSection('Title of action', $action, ['title' => 'title']);

      $status_field = $action->getTypeEntity()->getConfigurationElementByType('entity', 'status_field');
      $status_value = $this->getTempDataValue(['actions', $delta, 'primary_authority_status'], 'par_enforcement_notice_approve');
      $status = $action->getTypeEntity()->getAllowedFieldlabel($status_field, $status_value);
      $date = \Drupal::service('date.formatter')->format(time(), 'gds_date_format');
      $form['actions'][$delta]['status'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'value' => [
          '#type' => 'markup',
          '#markup' => $this->t('You %status this action on @date', ['%status' => $status, '@date' => $date]),
        ],
      ];

      if ($status_value === ParDataEnforcementAction::BLOCKED) {
        $reason = $this->getTempDataValue(['actions', $delta, 'primary_authority_notes'], 'par_enforcement_notice_approve');
      }
      elseif ($status_value === ParDataEnforcementAction::REFERRED) {
        $reason = $this->getTempDataValue(['actions', $delta, 'primary_authority_notes'], 'par_enforcement_notice_approve');
      }

      if (isset($reason)) {
        $form['actions'][$delta]['reason'] = [
          '#type' => 'fieldset',
          '#title' => 'Reason',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          'value' => [
            '#type' => 'markup',
            '#markup' => $reason,
          ],
        ];
      }

      $elements[$delta] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['actions'][$delta]['regulatory_function'] = $this->renderSection('Regulatory function', $action, ['field_regulatory_function' => 'title']);

      $form['actions'][$delta]['details'] = $this->renderSection('Details', $action, ['details' => 'full']);

      $form['actions'][$delta]['action_id'] = [
        '#type' => 'hidden',
        '#value' => $action->id()
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // @TODO Validate that any referred actions have a primary authority to refer to.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the notice so that the updates values are available for confirmation.
    if ($this->saveNotice($form, $form_state)) {
      $this->deleteStore();
    }
    else {
      $par_data_enforcement_notice = $this->getRouteParam('par_data_enforcement_notice');
      $message = $this->t('The enforcement notice %confirm could not be approved for %form_id');
      $replacements = [
        '%confirm' => $par_data_enforcement_notice->id(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }
  }

  /**
   * Helper function to save the notice.
   */
  public function saveNotice($form, $form_state) {
    $par_data_enforcement_notice = $this->getRouteParam('par_data_enforcement_notice');

    // Duplicate any referral actions.
    $referrals = [];
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form_data = $this->getTempDataValue(['actions', $delta], 'par_enforcement_notice_approve');
      if ($form_data['primary_authority_status'] === ParDataEnforcementAction::REFERRED) {
        // Duplicate this action before changing the status.
        $referrals[$delta] = $action->createDuplicate()->save();
      }
    }

    // If there are any referrals create a new notice for the referred to authority.
    $referred_to = $this->getTempDataValue('referred_to', 'par_enforcement_referred_authority');
    $primary_authority = ParDataAuthority::load($referred_to);
    if (!empty($referrals) && $primary_authority) {
      // Create the new notice.
      $referral_notice = $par_data_enforcement_notice->createDuplicate();
      $referral_notice->set('field_primary_authority', $primary_authority->id());

      // Loop through all referrals and create a new notice for them.
      foreach ($referrals as $delta => $referral) {
        $referral_notice->get('field_enforcement_action')->appendItem($primary_authority->id());
      }

      if (isset($referral_notice) && $referral_notice->save()) {
        // Persist the information in the temporary store here
        // because the application is not yet complete.
        $this->setTempDataValue('referral_id', $referral_notice->id());
        return $referral_notice;
      }
      else {
        $message = $this->t('Referred enforcement notice not saved on %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }

    // Loop through all the enforcement actions on the notice and save the status.
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form_data = $this->getTempDataValue(['actions', $delta], 'par_enforcement_notice_approve');

      if ($form_data['primary_authority_status'] === ParDataEnforcementAction::APPROVED) {

        // Approve the action and save.
        $action->approve();
      }
      elseif ($form_data['primary_authority_status'] === ParDataEnforcementAction::BLOCKED) {
        // Set the reason for blocking this action.
        $action->set('primary_authority_notes', $form_data['primary_authority_notes']);

        // Block the action and save.
        $action->block();
      }
      elseif ($form_data['primary_authority_status'] === ParDataEnforcementAction::REFERRED) {
        // Set the reason for referring this action.
        // And the authority this is being referred to.
        $action->set('primary_authority_notes', $form_data['primary_authority_notes']);

        try {
          if (!isset($referrals[$delta]) && $referrals[$delta]->id()) {
            $action->set('field_action_referral', $referrals[$delta]->id());
          }
          else{
            throw new ParDataException("The referal action could not be created, please contact the helpdesk.");
          }
        }
        catch (ParDataException $e) {
          $this->getLogger($this->getLoggerChannel())
            ->error($e->getMessage());
        }

        // Refer the action and save.
        $action->refer();
      }
    }
  }

}
