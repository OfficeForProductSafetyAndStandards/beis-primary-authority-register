<?php

namespace Drupal\par_enforcement_raise_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParReviewEnforcementForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function accessCallback(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    try {
      $this->getFlowNegotiator()->setRoute($route_match);
      $this->getFlowDataHandler()->reset();
      $this->loadData();
    } catch (ParFlowException $e) {

    }

    // Get the parameters for this route.
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    // This form should only be accessed if none of the enforcement notice actions have been acted on.
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      // Set an error if this action has already been reviewed.
      if ($action->isApproved() || $action->isBlocked() || $action->isReferred()) {
        $this->accessResult = AccessResult::forbidden('This action has already been reviewed.');
      }
    }
    return parent::accessCallback($route, $route_match, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return "Confirmation | Enforcement action decision";
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {

    $this->retrieveEditableValues($par_data_enforcement_notice);
    $enforcing_officer = current($par_data_enforcement_notice->getEnforcingPerson());

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

    $form['enforcement_officer_name'] = $this->renderSection('Enforcing officer name', $enforcing_officer, ['first_name' => 'summary','last_name' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_telephone'] = $this->renderSection('Enforcing officer telephone number', $enforcing_officer, ['work_phone' => 'summary'], [], TRUE, TRUE);
    $form['enforcement_officer_email'] = $this->renderSection('Enforcing officer email address', $enforcing_officer, ['email' => 'summary'], [], TRUE, TRUE);

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

      $cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');
      $status_value = $this->getFlowDataHandler()->getTempDataValue(['actions', $delta, 'primary_authority_status'], $cid);

      $date = \Drupal::service('date.formatter')->format(time(), 'gds_date_format');
      $form['actions'][$delta]['status'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        'value' => [
          '#type' => 'markup',
          '#markup' => $this->t('You %status this action on @date', ['%status' => $status_value, '@date' => $date]),
        ],
      ];

      if ($status_value === ParDataEnforcementAction::BLOCKED) {
        $reason = $this->getFlowDataHandler()->getTempDataValue(['actions', $delta, 'primary_authority_notes'], $cid);
      }
      elseif ($status_value === ParDataEnforcementAction::REFERRED) {
        $reason = $this->getFlowDataHandler()->getTempDataValue(['actions', $delta, 'referral_notes'], $cid);
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

    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($this->referral_cloning($par_data_enforcement_notice)) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The enforcement notice %confirm could not be approved for %form_id');
      $replacements = [
        '%confirm' => $par_data_enforcement_notice->id(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

  /**
   * Helper function to save the notice.
   */
  public function referral_cloning($par_data_enforcement_notice) {

    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {

      $cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');
      $form_data = $this->getFlowDataHandler()->getTempDataValue(['actions', $delta], $cid);
      $action_id = $action->id();

      switch ($form_data['primary_authority_status']) {
        case ParDataEnforcementAction::APPROVED:
          if (!$action->approve()) {
            $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a approved state within %form_id');
            $replacements = [
              '%entity_id' => $action_id,
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
            return FALSE;
          }
          break;

        case ParDataEnforcementAction::BLOCKED:
          if (!$action->block($form_data['primary_authority_notes'])) {
            $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a blocked state within %form_id');
            $replacements = [
              '%entity_id' => $action_id,
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
            return FALSE;
          }
          break;

        case ParDataEnforcementAction::REFERRED:

          // The authority to refer the current action to.
          $cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_referred_authority');
          $referral_authority_id = $this->getFlowDataHandler()->getTempDataValue([$action_id], $cid);

          try {
            $cloned_action = $action->cloneReferredEnforcementAction($referral_authority_id, $action_id);
          }
          catch (ParDataException $e) {
            $replacements = [
              '%action' => $action_id,
            ];
            $this->getLogger($this->getLoggerChannel())->error($e->getMessage(), $replacements);
            return FALSE;
          }

          if (isset($cloned_action)) {
            try {
              // Create a new Enforcement notice for every referred action.
              $referral_notice = $action->cloneEnforcementNotice($referral_authority_id, $cloned_action, $par_data_enforcement_notice);
            }
            catch (ParDataException $e) {
              $replacements = [
                '%action' => $action_id,
              ];
              $this->getLogger($this->getLoggerChannel())->error($e->getMessage(), $replacements);
              return FALSE;
            }
            if (isset($referral_notice)) {
              // Persist the information in the temporary store here
              // because the application is not yet complete.
              $this->getFlowDataHandler()->setTempDataValue('referral_id' . $action_id, $referral_notice->id());

              if (!$action->refer($form_data['referral_notes'])) {
                $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a referred state within %form_id');
                $replacements = [
                  '%entity_id' => $action_id,
                  '%form_id' => $this->getFormId(),
                ];
                $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
                return FALSE;
              }
            }
          }
          break;
      }
    }
    return TRUE;
  }

}
