<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\Core\Access\AccessResult;
use Drupal\par_flows\ParDisplayTrait;
use Drupal\par_flows\ParFlowException;
use Symfony\Component\Routing\Route;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The confirming the user is authorised to archive partnerships.
 */
class ParPartnershipFlowsRevokeInspectionPlanForm extends ParBaseForm {

  use ParDisplayTrait;
  use ParPartnershipFlowAccessTrait;
  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['archive_reason', 'par_data_inspection_plan', 'archive_reason', NULL, NULL, 0, [
      'This value should not be null.' => 'Please supply the reason for archiving this document.'
    ]],
  ];



  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return 'Are you sure you want to archive this inspection plan?';
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
        '#title' => $this->t('Archive denied'),
        '#markup' => $this->t('This inspection plan document cannot be archived because the partnership it is awaiting approval or there are enforcement notices currently awaiting review. Please try again later.'),
      ];

      return parent::buildForm($form, $form_state);
    }

    $form['inspection_plan_info'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Archive the inspection plan'),
      '#attributes' => ['class' => 'form-group'],
    ];

    $form['inspection_plan_info']['inspection_plan_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_inspection_plan->label(),
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    // Enter the archive reason.
    $form['delete_reason'] = [
      '#title' => $this->t('Enter the reason you are revoking this inspection plan'),
      '#type' => 'textarea',
      '#rows' => 5,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('archive_reason', FALSE),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('archive_reason')) {
      $id = $this->getElementId('archive_reason', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please supply the reason for archiving this document.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    // We only want to update the status of active inspection plan documents.
    if (!$par_data_inspection_plan->isArchived()) {

      $reason = $this->getFlowDataHandler()->getTempDataValue('archive_reason');
      $archived = $par_data_inspection_plan->inspection_plan_archive(TRUE, $reason);

      if ($archived) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('Archive reason could not be saved for %form_id');
        $replacements = [
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())
          ->error($message, $replacements);
      }

    }
  }

}
