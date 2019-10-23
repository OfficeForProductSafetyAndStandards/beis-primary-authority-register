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

}
