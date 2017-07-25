<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
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
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_details_terms';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param ParDataAuthority $par_data_authority
   *   The Authority being retrieved.
   * @param ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // We need to get the value of the terms and conditions checkbox.

    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_authority, $par_data_partnership);

    // If the terms and conditions have already been set
    // we want to go immediately to the next step.
    if ($this->getDefaultValues('terms_conditions', FALSE)) {
      return $this->redirect($this->getFlow()->getNextRoute(), $this->getRouteParams());
    }

    $form['terms_summary'] = [
      '#markup' => "Please Review the new Primary Authority terms and conditions and confirm that you agree with them.<br>The New terms will come into effect from <em>01 October 2017</em>.<br>What's changed?",
    ];

    // @stemont will need your input on componentizing this summary box.
    $form['terms_summary'] = [
      '#markup' => "<ul><li>The scheme is expanding to include more types of businesses.</li><li>The process of revoking a partnership will be more formalised.</li><li>The process for updating an inspection plan has been updated.</li></ul>",
    ];

    // Partnership details.
    $form['terms_conditions'] = [
      '#type' => 'checkbox',
      '#title' => t('(NOT YET SAVED) Iconfirm that my authority agrees to the new Terms and Conditions.'),
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getRouteParam('par_data_partnership');
    // @TODO Still need to save/acknowledge the acceptance of the terms.
    if ($partnership->save()) {
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
