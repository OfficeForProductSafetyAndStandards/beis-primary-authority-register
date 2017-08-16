<?php

namespace Drupal\par_flow_transition_business\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionTermsForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_business';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_business_terms';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // We need to get the value of the terms and conditions checkbox.
      $terms_value = !empty($par_data_partnership->get('terms_organisation_agreed')->getString());
      $this->loadDataValue('terms_conditions', $terms_value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);

    // If the terms and conditions have already been set
    // we want to go immediately to the next step.
    if ($this->getDefaultValues('terms_conditions', FALSE)) {
      return $this->redirect($this->getFlow()->getNextRoute(), $this->getRouteParams());
    }

    $form['terms_intro'] = [
      '#markup' => "<p>Please Review the new Primary Authority terms and conditions and confirm that you agree with them.</p><p>The New terms will come into effect from <em>01 October 2017</em>.</p>",
    ];

    // Partnership agree terms.
    $form['terms_conditions'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that my authority agrees to the new Terms and Conditions.'),
      '#disabled' => $this->getDefaultValues("terms_organisation_agreed"),
      '#return_value' => 'on',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);

    if (empty($form_state->getValue('terms_conditions'))) {
      $form_state->setErrorByName('terms_conditions', $this->t('<a href="#edit-terms-conditions">You must agree to the new terms and conditions.</a>'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

    // Save the value for the terms field.
    if ($terms_conditions_value = $this->decideBooleanValue($this->getTempDataValue('terms_conditions'))) {
      $par_data_partnership->set('terms_organisation_agreed', $terms_conditions_value);
    }

    if ($par_data_partnership->save()) {
      $this->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'about_partnership',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

    // Go back to the overview.
    $form_state->setRedirect($this->getFlow()->getNextRoute(), $this->getRouteParams());
  }

}
