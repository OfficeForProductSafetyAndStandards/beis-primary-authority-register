<?php

namespace Drupal\par_flow_transition_partnership_details\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionFirstForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_partnership_details';

  public function getFormId() {
    return 'par_flow_transition_partnership_details_about';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $authority = NULL, ParDataPartnership $partnership = NULL) {
    if ($partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$partnership->id()}");

      // If we want to use values already saved we have to tell
      // the form about them.
      $this->loadDataValue('about_partnership', $partnership->get('about_partnership')->getValue());
    }

    $form['about_partnership'] = [
      '#type' => 'textfield',
      '#title' => $this->t('About'),
      '#description' => $this->t('Use this section to give a brief overview of the project.<br>Include any information you feel may be useful to enforcing authorities.'),
      '#default_value' => $this->getDefaultValues('about_partnership'),
      '#required' => TRUE,
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
    ];

    return $form;
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
    if ($partnership->setValue($this->getTempDataValue('about_partnership'))->save()) {
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
    $form_state->setRedirect('par_flow_transition_partnership_details.overview', $this->getRouteParams());
  }
}
