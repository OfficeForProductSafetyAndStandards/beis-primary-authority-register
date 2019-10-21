<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The inspection plan document form.
 */
class ParPartnershipFlowsInspectionPlanForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['title', 'par_data_inspection_plan', 'title', NULL, NULL, 0, [
      'This value should not be null.' => 'You must provide a title for this inspection plan document.'
    ]],
    ['summary', 'par_data_inspection_plan', 'summary', NULL, NULL, 0, [
      'This value should not be null.' => 'You must provide a summary for this inspection plan document.'
    ]],
  ];


  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    $verb = $par_data_inspection_plan ? 'Edit' : 'Add';
    $this->pageTitle = "$verb inspection plan details";

    return parent::titleCallback();
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataInspectionPlan $par_data_inspection_plan
   *   The inspection plan document being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    if ($par_data_inspection_plan) {
      // Inspection plan title.
      $title = $par_data_inspection_plan->get('title')->getString();
      if (isset($title)) {
        $this->getFlowDataHandler()->setFormPermValue('title', $title);
      }

      // Inspection plan summary.
      $notes = $par_data_inspection_plan->get('summary')->getString();
      if (isset($notes)) {
        $this->getFlowDataHandler()->setFormPermValue('summary', $notes);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataInspectionPlan $par_data_inspection_plan = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_inspection_plan);
    $Inspection_plan_bundle = $this->getParDataManager()->getParBundleEntity('par_data_inspection_plan');

    // Get files from "upload" step.
    $cid = $this->getFlowNegotiator()->getFormKey('upload');
    $files = $this->getFlowDataHandler()->getDefaultValues("inspection_plan_files", '', $cid);
    if ($files) {
      // Show files.
      foreach ($files as $file) {
        $file = File::load($file);

        $form['file'][] = [
          '#type' => 'markup',
          '#prefix' => '<p class="file">',
          '#suffix' => '</p>',
          '#markup' => $file->getFileName()
        ];
      }
    }

    // The inspection plan title.
    $form['title'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Inspection plan title')  . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('title'),
    ];

    // The inspection plan summary.
    $form['summary'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Provide summarised details of this inspection plan') . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('summary'),
      '#description' => '<p>Use this section to give a brief overview of the inspection plan document, include any information you feel may be useful to someone to search for this inspection plan.</p>',
    ];

    // Make sure to add the document cacheability data to this form.
    $this->addCacheableDependency($par_data_inspection_plan);
    $this->addCacheableDependency($Inspection_plan_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('inspection_plan_expire')){
      $id = $this->getElementId('inspection_plan_expire', $form);
      $form_state->setErrorByName($this->getElementName(['inspection_plan_expire']), $this->wrapErrorMessage('You must enter the date the inspection plan expires e.g. 30 - 01 - 2022', $id));
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
    $cid = $this->getFlowNegotiator()->getFormKey('upload');
    $files = $this->getFlowDataHandler()->getDefaultValues('inspection_plan_files', [], $cid);

    // Add all the uploaded files from the upload form to the inspection plan and save.
    $files_to_add = [];

    foreach ($files as $file) {
      $file = File::load($file);
      if ($file->isTemporary()) {
        $file->setPermanent();
        $file->save();
      }
      $files_to_add[] = $file->id();
    }

    $request_date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);

    // Create new inspection plan if needed.
    if (!$par_data_inspection_plan) {
      $par_data_inspection_plan = ParDataInspectionPlan::create([
        'type' => 'inspection_plan',
        'uid' => 1,
        'issue_date' => $request_date->format("Y-m-d"),
      ]);
    }

    // Set the inspection plan title.
    $par_data_inspection_plan->set('title', $this->getFlowDataHandler()->getTempDataValue('title'));

    // Set the inspection plan summary.
    $par_data_inspection_plan->set('summary', $this->getFlowDataHandler()->getTempDataValue('summary'));

    // Set the status to active for the inspection plan entity.
    $par_data_inspection_plan->setParStatus('current', TRUE);

    // Add files if required.
    if ($files_to_add) {
      $par_data_inspection_plan->set('document', $files_to_add);
    }

    if ($par_data_inspection_plan->isNew()) {
      $inspection_plan_start_date =  $request_date->format("Y-m-d");
    }
    else {
      $inspection_plan_start_date =  $par_data_inspection_plan->get('valid_date')->value;
    }

    // set the expire time for inspection plan entities.
    $inspection_plan_end_date = $this->getFlowDataHandler()->getTempDataValue('inspection_plan_expire');
    // Set date range values for inspection plans.
    $par_data_inspection_plan->set('valid_date', ['value' => $inspection_plan_start_date, 'end_value' => $inspection_plan_end_date]);

    // Save and attach new inspection plan entities.
    if ($par_data_inspection_plan->isNew() && $par_data_inspection_plan->save()) {
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->get('field_inspection_plan')->appendItem($par_data_inspection_plan->id());

      if ($par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %inspection plan could not be created for %form_id');
        $replacements = [
          '%inspection plan' => $par_data_inspection_plan->label(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    // Save existing inspection plan entities.
    else if ($par_data_inspection_plan->save()) {
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
