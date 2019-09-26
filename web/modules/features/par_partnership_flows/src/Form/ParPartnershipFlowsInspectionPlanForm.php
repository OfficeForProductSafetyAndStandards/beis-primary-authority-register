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
      $Inspection_plan_title = $par_data_inspection_plan->get('inspection_plan_title')->getString();
      if (isset($Inspection_plan_title)) {
        $this->getFlowDataHandler()->setFormPermValue('inspection_plan_title', $Inspection_plan_title);
      }

      // Inspection plan summary.
      $notes = $par_data_inspection_plan->get('inspection_plan_summary')->getString();
      if (isset($notes)) {
        $this->getFlowDataHandler()->setFormPermValue('inspection_plan_summary', $notes);
      }

      // Get Regulatory Functions.
      $regulatory_functions = $par_data_inspection_plan->get('field_regulatory_function')->referencedEntities();
      $regulatory_function_options = $this->getParDataManager()->getEntitiesAsOptions($regulatory_functions);
      $this->getFlowDataHandler()->setFormPermValue('regulatory_functions', $regulatory_function_options);
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
    $files = $this->getFlowDataHandler()->getDefaultValues("files", '', $cid);
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
    $form['inspection_plan_title'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Inspection plan title')  . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('inspection_plan_title'),
    ];

    // The inspection plan summary.
    $form['inspection_plan_summary'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Provide summarised details of this inspection plan') . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('inspection_plan_summary'),
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
    parent::validateForm($form, $form_state);

    $submitted_reg_function = $form_state->getValue('regulatory_functions');

    if (empty($submitted_reg_function)) {
      $id = $this->getElementId(['regulatory_functions'], $form);
      $form_state->setErrorByName($this->getElementName('regulatory_functions'), $this->wrapErrorMessage('You must select at least one regulatory function.', $id));
    };
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
    $files = $this->getFlowDataHandler()->getDefaultValues('files', [], $cid);

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

    // Create new inspection plan if needed.
    if (!$par_data_inspection_plan) {
      $request_date = DrupalDateTime::createFromTimestamp(time(), NULL, ['validate_format' => FALSE]);
      $par_data_inspection_plan = ParDataInspectionPlan::create([
        'type' => 'inspection plan',
        'uid' => 1,
        'issue_date' => $request_date->format("Y-m-d"),
      ]);
    }
    $allowed_types = $par_data_inspection_plan->getTypeEntity()->getAllowedValues('advice_type');

    // Set the inspection plan title.
    $par_data_inspection_plan->set('advice_title', $this->getFlowDataHandler()->getTempDataValue('advice_title'));

    // Set the inspection plan summary.
    $par_data_inspection_plan->set('notes', $this->getFlowDataHandler()->getTempDataValue('notes'));

    // Set the inspection plan type.
    $Inspection_plan_type = $this->getFlowDataHandler()->getTempDataValue('advice_type');
    if (isset($allowed_types[$Inspection_plan_type])) {
      $par_data_inspection_plan->set('advice_type', $Inspection_plan_type);
    }

    // Set regulatory functions.
    $par_data_inspection_plan->set('field_regulatory_function', $this->getFlowDataHandler()->getTempDataValue('regulatory_functions'));

    // Set the status to active for the inspection plan entity.
    $par_data_inspection_plan->setParStatus('active', TRUE);

    // Add files if required.
    if ($files_to_add) {
      $par_data_inspection_plan->set('document', $files_to_add);
    }

    // Save and attach new inspection plan entities.
    if ($par_data_inspection_plan->isNew() && $par_data_inspection_plan->save()) {
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->get('field_advice')->appendItem($par_data_inspection_plan->id());

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
