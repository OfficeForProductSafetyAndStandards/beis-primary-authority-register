<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * The advice document form.
 */
class ParPartnershipFlowsAdviceForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  protected $entityMapping = [
    ['advice_title', 'par_data_advice', 'advice_title', NULL, NULL, 0, [
      'This value should not be null.' => 'You must provide a title for this advice document.'
    ]],
    ['notes', 'par_data_advice', 'notes', NULL, NULL, 0, [
      'This value should not be null.' => 'You must provide a summary for this advice document.'
    ]],
    ['advice_type', 'par_data_advice', 'advice_type', NULL, NULL, 0, [
      'This value should not be null.' => 'You must choose what type of advice this is.'
    ]],
    ['regulatory_functions', 'par_data_advice', 'field_regulatory_function', NULL, NULL, 0, [
      'This value should not be null.' => 'You must choose which regulatory functions this advice applies to.'
    ]],
  ];


  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    return "Primary Authority Advice | Edit advice details";
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice document being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    if ($par_data_advice) {
      // Partnership Confirmation.
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');
      if (!$this->currentUser()->hasPermission('update primary authority advice to local authorities')) {
        unset($allowed_types['authority_advice']);
      }
      $advice_type = $par_data_advice->get('advice_type')->getString();
      if (isset($allowed_types[$advice_type])) {
        $this->getFlowDataHandler()->setFormPermValue('advice_type', $advice_type);
      }
      // Advice title.
      $advice_title = $par_data_advice->get('advice_title')->getString();
      if (isset($advice_title)) {
        $this->getFlowDataHandler()->setFormPermValue('advice_title', $advice_title);
      }

      // Advice summary.
      $notes = $par_data_advice->get('notes')->getString();
      if (isset($notes)) {
        $this->getFlowDataHandler()->setFormPermValue('notes', $notes);
      }

      // Get Regulatory Functions.
      $regulatory_functions = $par_data_advice->get('field_regulatory_function')->referencedEntities();
      $regulatory_options = [];
      foreach ($regulatory_functions as $function) {
        $regulatory_options[$function->id()] = $function->id();
      }
      $this->getFlowDataHandler()->setFormPermValue('regulatory_functions', $regulatory_options);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_advice);
    $advice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_advice');

    // Get files from "upload" step.
    // To use this form there must be a "form_data['upload']" key in the step configuration:
    // 1:
    //   route: example.route_name
    //   form_id: form_id_where_file_is_uploaded
    //   form_data:
    //     upload: par_partnership_advice_upload_edit
    // 2:
    //   route: example.route_name_2
    //   form_id: example_form_id
    //   form_data:
    //     upload: form_id_where_file_is_uplaoded
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

    $allowed_types = $advice_bundle->getAllowedValues('advice_type');
    if (!$this->currentUser()->hasPermission('update primary authority advice to local authorities')) {
      unset($allowed_types['authority_advice']);
    }

    // The advice title.
    $form['advice_title'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Advice title')  . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('advice_title'),
    ];

    // The advice summary.
    $form['notes'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => '<h3 class="heading-medium">' . $this->t('Provide summarised details of this advice') . '</h3>',
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('notes'),
      '#description' => '<p>Use this section to give a brief overview of the advice document.</p><p>Include any information you feel may be useful.</p>',
    ];

    // The advice type.
    $form['advice_type'] = [
      '#type' => 'radios',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('Type of advice'),
      '#options' => $allowed_types,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('advice_type'),
    ];

    // The regulatory functions of the advice entity.
    $regulatory_function_options = $par_data_partnership->getEntityFieldAsOptions('field_regulatory_function');

    $default_reg_function = $this->getFlowDataHandler()->getDefaultValues('regulatory_functions', []);

    if (is_array($default_reg_function) && count($default_reg_function) > 1) {
      $default_reg_function = [];
    }
    elseif (!empty($default_reg_function)) {
      $default_reg_function = key($default_reg_function);
    }

    $form['regulatory_functions'] = [
      '#type' => 'radios',
      '#attributes' => [
        'class' => ['form-group'],
      ],
      '#title' => $this->t('Regulatory functions this advice covers'),
      '#options' => $regulatory_function_options,
      '#default_value' => $default_reg_function,
    ];

    // Make sure to add the document cacheability data to this form.
    $this->addCacheableDependency($par_data_advice);
    $this->addCacheableDependency($advice_bundle);

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

    // Get the advice entity from the URL.
    $par_data_advice = $this->getFlowDataHandler()->getParameter('par_data_advice');

    // Get files from "upload" step.
    // To use this form there must be a "form_data['upload']" key in the step configuration:
    // 1:
    //   route: example.route_name
    //   form_id: form_id_where_file_is_uplaoded
    //   form_data:
    //     upload: par_partnership_advice_upload_edit
    // 2:
    //   route: example.route_name_2
    //   form_id: example_form_id
    //   form_data:
    //     upload: form_id_where_file_is_uplaoded
    $cid = $this->getFlowNegotiator()->getFormKey('upload');
    $files = $this->getFlowDataHandler()->getDefaultValues('files', [], $cid);

    // Add all the uploaded files from the upload form to the advice and save.
    $files_to_add = [];

    foreach ($files as $file) {
      $file = File::load($file);
      if ($file->isTemporary()) {
        $file->setPermanent();
        $file->save();
      }
      $files_to_add[] = $file->id();
    }

    if ($par_data_advice) {
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');
      $advice_type = $this->getFlowDataHandler()->getTempDataValue('advice_type');
      $advice_title = $this->getFlowDataHandler()->getTempDataValue('advice_title');
      $notes = $this->getFlowDataHandler()->getTempDataValue('notes');

      // Check if title data has been stored.
      if ($advice_title) {
        $par_data_advice->set('advice_title', $advice_title);
      }

      // Check if advice_summary data has been stored.
      if ($notes) {
        $par_data_advice->set('notes', $notes);
      }

      if (isset($allowed_types[$advice_type])) {
        $par_data_advice->set('advice_type', $advice_type);
      }

      $regulatory_functions_selected = $this->getFlowDataHandler()->getTempDataValue('regulatory_functions');
      $par_data_advice->set('field_regulatory_function', $regulatory_functions_selected);

      // Check if there are files to add from the Advice Upload form.
      if ($files_to_add) {
        $par_data_advice->set('document', $files_to_add);
      }

      // Set the issue date value for the advice entity.
      $time = new \DateTime();
      $par_data_advice->set('issue_date', $time->format("d--m-Y"));

      // Set the status to active for the advice entity.
      $par_data_advice->set('advice_status', 'active');

      if ($par_data_advice->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %advice could not be saved for %form_id');
        $replacements = [
          '%advice' => $par_data_advice->label(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Create new advice entity.
      $par_data_advice = ParDataAdvice::create([
        'type' => 'advice',
        'uid' => 1,
      ]);

      // Check if there are files to add from the Advice Upload form.
      if ($files_to_add) {
        $par_data_advice->set('document', $files_to_add);
      }

      $advice_title = $this->getFlowDataHandler()->getTempDataValue('advice_title');

      if (isset($advice_title)) {
        $par_data_advice->set('advice_title', $advice_title);
      }

      $notes = $this->getFlowDataHandler()->getTempDataValue('notes');

      if (isset($notes)) {
        $par_data_advice->set('notes', $notes);
      }

      // Set advice type.
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');

      $advice_type = $this->getFlowDataHandler()->getTempDataValue('advice_type');

      if (isset($allowed_types[$advice_type])) {
        $par_data_advice->set('advice_type', $advice_type);
      }

      // Set regulatory functions.
      $regulatory_functions_selected = $this->getFlowDataHandler()->getTempDataValue('regulatory_functions');

      $par_data_advice->set('field_regulatory_function', $regulatory_functions_selected);

      // Save new advice entity.
      $par_data_advice->save();

      // Get partnership entity from URL.
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

      // Combine current pieces of advice to prevent overwriting field.
      $partnership_advice = array_merge($par_data_partnership->retrieveEntityIds('field_advice'), [$par_data_advice->id()]);

      // Update field_advice with our new advice ID.
      $par_data_partnership->set('field_advice', $partnership_advice);

      // Save partnership.
      if ($par_data_partnership->save()) {
        $this->getFlowDataHandler()->deleteStore();
      }
      else {
        $message = $this->t('This %partnership could not be saved for %form_id');
        $replacements = [
          '%partnership' => $par_data_partnership->label(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
  }

}
