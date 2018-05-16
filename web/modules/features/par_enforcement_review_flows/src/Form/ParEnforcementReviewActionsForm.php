<?php

namespace Drupal\par_enforcement_review_flows\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataException;
use Drupal\par_enforcement_review_flows\ParFlowAccessTrait;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementReviewActionsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Respond to notice of enforcement actions | Review";

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    /** @var ParDataEnforcementAction[] $par_data_enforcement_actions */

    // Return path for all redirect links.
    $return_path = UrlHelper::encodePath(\Drupal::service('path.current')->getPath());

    // Display the Enforcement Notice details.
    $form['enforcement_type'] = $this->renderSection('Type of enforcement notice', $par_data_enforcement_notice, ['notice_type' => 'full'], [], TRUE, TRUE);

    $form['enforcement_summary'] = $this->renderSection('Summary of enforcement notice', $par_data_enforcement_notice, ['summary' => 'summary'], [], TRUE, TRUE);

    // Display the details for each Enforcement Action.
    $form['enforcement_actions'] = $this->renderEntities('Enforcement Actions', $par_data_enforcement_actions, 'summary');

    return parent::buildForm($form, $form_state);
  }

  public function createEntities() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $enforcement_actions_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');
    $enforcement_referral_cid = $this->getFlowNegotiator()->getFormKey('referrals');

    // Create the enforcement actions.
    foreach ($par_data_enforcement_actions as $par_data_enforcement_action) {
      $status = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'action', 'primary_authority_status'], $enforcement_actions_cid);
var_dump($status);
      switch ($status) {
        case ParDataEnforcementAction::APPROVED:
          $par_data_enforcement_action->approve(FALSE);
          break;

        case ParDataEnforcementAction::BLOCKED:
          $notes = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'action', 'primary_authority_notes'], $enforcement_actions_cid);
          $par_data_enforcement_action->block($notes, FALSE);
          break;

        case ParDataEnforcementAction::REFERRED:
          $notes = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'action', 'referral_notes'], $enforcement_actions_cid);
          $par_data_enforcement_action->refer($notes, FALSE);
          break;
      }
    }

    return [
      'par_data_enforcement_notice' => $par_data_enforcement_notice,
      'par_data_enforcement_actions' => $par_data_enforcement_actions,
    ];
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
