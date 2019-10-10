<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The revoke inspection plan form.
 */
class ParPartnershipFlowsRevokeInspectionPlanForm extends ParBaseForm {

  use ParDisplayTrait;
  use ParPartnershipFlowAccessTrait;
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    [ParDataEntity::REVOKE_REASON_FIELD, 'par_data_inspection_plan', ParDataEntity::REVOKE_REASON_FIELD, NULL, NULL, 0, [
      'This value should not be null.' => 'Please supply the reason for revoking this document.'
    ]],
  ];



  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Are you sure you want to revoke this inspection plan?';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    if ($par_data_partnership && $par_data_partnership->inProgress()) {
      $form['inspection_plan_info'] = [
        '#type' => 'markup',
        '#title' => $this->t('Revoke denied'),
        '#markup' => $this->t('This inspection plan document cannot be revoked because the partnership it is awaiting approval or there are enforcement notices currently awaiting review. Please try again later.'),
      ];

      return parent::buildForm($form, $form_state);
    }

    $form['inspection_plan_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Revoke the inspection plan'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['inspection_plan_info']['inspection_plan_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_inspection_plan->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Enter the revoke reason.
    $form[ParDataEntity::REVOKE_REASON_FIELD] = [
      '#title' => $this->t('Enter the reason you are revoking this inspection plan'),
      '#type' => 'textarea',
      '#rows' => 5,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues(ParDataEntity::REVOKE_REASON_FIELD, FALSE),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue(ParDataEntity::REVOKE_REASON_FIELD)) {
      $id = $this->getElementId(ParDataEntity::REVOKE_REASON_FIELD, $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please supply the reason for revoking this document.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    // We only want to update the status of active inspection plan documents.
    if (!$par_data_inspection_plan->isRevoked()) {

      $revoke_reason = $this->getFlowDataHandler()->getTempDataValue(ParDataEntity::REVOKE_REASON_FIELD);
      $revoked = $par_data_inspection_plan->revoke(TRUE, $revoke_reason);

      if ($revoked) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Revoke reason could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

    }
  }

}
