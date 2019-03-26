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

  public function loadData() {
    // Set the data values on the entities
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    /** @var ParDataEnforcementAction[] $par_data_enforcement_actions */

    if ($par_data_enforcement_actions) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_detail', $par_data_enforcement_actions);
    }

    parent::loadData();
  }

  public function createEntities() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');
    $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions();

    // Get the cache IDs for the various forms that needs needs to be extracted from.
    $enforcement_actions_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_notice_approve');
    $enforcement_referral_cid = $this->getFlowNegotiator()->getFormKey('referrals');

    // Create the enforcement actions.
    foreach ($par_data_enforcement_actions as $delta => $par_data_enforcement_action) {
      $status = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'primary_authority_status'], $enforcement_actions_cid);

      switch ($status) {
        case ParDataEnforcementAction::APPROVED:
          $approved = $par_data_enforcement_action->approve(FALSE);
          break;

        case ParDataEnforcementAction::BLOCKED:
          $notes = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'primary_authority_notes'], $enforcement_actions_cid);
          $par_data_enforcement_action->block($notes, FALSE);
          break;

        case ParDataEnforcementAction::REFERRED:
          $notes = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $delta, 'referral_notes'], $enforcement_actions_cid);
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
    parent::validateForm($form, $form_state);

    // @TODO Validate that any referred actions have a primary authority to refer to.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Set the correct values for the entities to be saved.
    $entities = $this->createEntities();
    extract($entities);
    /** @var ParDataEnforcementNotice $par_data_enforcement_notice */
    /** @var ParDataEnforcementAction[] $par_data_enforcement_actions */

    $action_referral_cid = $this->getFlowNegotiator()->getFormKey('par_enforcement_referred_authority');

    // Iterate through the actions and perform any necessary updates.
    foreach ($par_data_enforcement_actions as $delta => $par_data_enforcement_action) {
      $action_saved = $par_data_enforcement_action->save();

      // Skip any actions that are not being referred.
      if (!$par_data_enforcement_action->isReferred()) {
        continue;
      }

      // The authority to refer the current action to.
      $referral_authority_id = $this->getFlowDataHandler()->getTempDataValue([ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_refer', $delta, 'referred_to'], $action_referral_cid);

      try {
        if ($cloned_action = $par_data_enforcement_action->cloneAction($referral_authority_id)) {
          $cloned_notice = $par_data_enforcement_notice->cloneNotice($referral_authority_id, $cloned_action);
        }
      }
      catch (ParDataException $e) {
        $replacements = [
          '%action' => $par_data_enforcement_action->id(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($e->getMessage(), $replacements);
        return FALSE;
      }

      if (isset($cloned_notice) && !empty($cloned_notice) && $action_saved) {
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

    // Save the enforcement notice.
    if ($par_data_enforcement_notice->save()) {
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
}
