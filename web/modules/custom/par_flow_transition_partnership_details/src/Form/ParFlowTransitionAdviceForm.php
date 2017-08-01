<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The advice document form for Transition Journey 1.
 */
class ParFlowTransitionAdviceForm extends ParBaseForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_advice_document';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param ParDataAdvice $par_data_inspection_plan
   *   The advice document being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    if ($par_data_partnership && $par_data_advice) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()},{$par_data_advice->id()}");

      // Partnership Confirmation.
      $allowed_types = $par_data_advice->type->entity->getConfigurationByType('advice_type', 'allowed_values');
      // Set the on and off values so we don't have to do that again.
      $this->loadDataValue('allowed_types', $allowed_types);
      $advice_type = $par_data_advice->get('advice_type')->getString();
      if (is_array($allowed_types) && in_array($advice_type, $allowed_types)) {
        $this->loadDataValue('document_type', $advice_type);
      }

      // @TODO We need to work out how to get a list of regulatory functions.
      if ($we_know_the_regulatory_functions = FALSE) {
        $this->loadDataValue('regulatory_functions', []);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_advice);

    // Render the document in view mode to allow users to
    // see which one they're confirming details for.
    // @TODO We don't have a reference to the document yet.
    $document_view_builder = $par_data_advice ? $par_data_advice->getViewBuilder() : NULL;
    $document = $document_view_builder->view($par_data_advice, 'summary');
    $form['document'] = $this->renderMarkupField($document) + [
      '#title' => $this->t('Document'),
    ];

    // The Person's work phone number.
    $form['document_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Type of Document'),
      '#options' => $this->getDefaultValues("allowed_types", []),
      '#default_value' => $this->getDefaultValues("document_type"),
      '#required' => TRUE,
    ];

    // The Person's work phone number.
    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Regulatory functions this document covers'),
      '#options' => $this->getDefaultValues("allowed_types", []),
      '#default_value' => $this->getDefaultValues("regulatory_functions", []),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    // Make sure to add the document cacheability data to this form.
    $this->addCacheableDependency($par_data_advice);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $person = $this->getRouteParam('par_data_person');
    $person->set('salutation', $this->getTempDataValue('salutation'));
    $person->set('person_name', $this->getTempDataValue('person_name'));
    $person->set('work_phone', $this->getTempDataValue('work_phone'));
    $person->set('mobile_phone', $this->getTempDataValue('mobile_phone'));
    $person->set('email', $this->getTempDataValue('email'));
    if ($person->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('This %person could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getTempDataValue('person_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getRouteByStep(4), $this->getRouteParams());
  }
}
