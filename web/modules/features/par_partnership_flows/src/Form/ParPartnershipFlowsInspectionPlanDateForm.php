<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The inspection plan expiry date form.
 */
class ParPartnershipFlowsInspectionPlanDateForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $this->pageTitle = $inspection_plan ? 'Change the expiry date' : 'When does this inspeciton plan expire?';

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $expiry = $form_state->getValue('expire');

    // Check date is in the future
    if ($expiry) {
      $request_date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
      $expiry_date = DrupalDateTime::createFromFormat('Y-m-d', $expiry, NULL, ['validate_format' => FALSE]);

      if ($request_date >= $expiry_date) {
        $id = $this->getElementId('expire', $form);
        $form_state->setErrorByName($this->getElementName(['expire']), $this->wrapErrorMessage('The inspection plan expiry date must be in the future e.g. 30 - 01 - 2022', $id));
      }
    }
    else {
      $id = $this->getElementId('expire', $form);
      $form_state->setErrorByName($this->getElementName(['expire']), $this->wrapErrorMessage('You must enter the date the inspection plan expires e.g. 30 - 01 - 2022', $id));
    }

    parent::validateForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the inspection plan entity from the URL.
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    // Get files from "upload" step.
    $file_upload_cid = $this->getFlowNegotiator()->getFormKey('upload');
    $inspection_details_cid = $this->getFlowNegotiator()->getFormKey('details');

    // Create new inspection plan if needed.
    if (!$par_data_inspection_plan) {
      $par_data_inspection_plan = ParDataInspectionPlan::create([
        'type' => 'inspection_plan',
      ]);
    }

    // Set the inspection plan title.
    $par_data_inspection_plan->set('title', $this->getFlowDataHandler()->getDefaultValues('title', '', $inspection_details_cid));

    // Set the inspection plan summary.
    $par_data_inspection_plan->set('summary', $this->getFlowDataHandler()->getDefaultValues('summary', '', $inspection_details_cid));

    // Add files if required.
    $file_ids = $this->getFlowDataHandler()->getDefaultValues('inspection_plan_files', [], $file_upload_cid);
    $files = !empty($file_ids) ? File::loadMultiple((array) $file_ids) : NULL;
    if ($files) {
      $par_data_inspection_plan->set('document', $files);
    }

    $request_date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
    if ($par_data_inspection_plan->isNew()) {
      $inspection_plan_start_date =  $request_date->format("Y-m-d");
    }
    else {
      $inspection_plan_start_date =  $par_data_inspection_plan->get('valid_date')->value;
    }

    // set the expire time for inspection plan entities.
    $inspection_plan_end_date = $this->getFlowDataHandler()->getTempDataValue('expire');
    // Set date range values for inspection plans.
    $par_data_inspection_plan->set('valid_date', ['value' => $inspection_plan_start_date, 'end_value' => $inspection_plan_end_date]);

    $is_new = $par_data_inspection_plan->isNew();

    // Save and attach new inspection plan entities.
    if ($par_data_inspection_plan->save()) {
      if ($is_new) {
        $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
        $par_data_partnership->get('field_inspection_plan')->appendItem($par_data_inspection_plan->id());
        $par_data_partnership->save();
      }

      $this->getFlowDataHandler()->deleteStore();
    }
    // Log an error.
    else {
      $message = $this->t('This %inspection plan could not be saved for %form_id');
      $replacements = [
        '%inspection plan' => $par_data_inspection_plan->label(),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
