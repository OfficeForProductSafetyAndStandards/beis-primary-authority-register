<?php

namespace Drupal\par_flow_transition_coordinator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Form\ParFlowTransitionAboutForm as ParFlowTransitionAboutBusinessForm;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionAboutForm extends ParFlowTransitionAboutBusinessForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_coordinator_about';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    return parent::buildForm($form, $form_state, $par_data_partnership);
  }

  /**
   * {@inheritdoc}
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

  }

}
