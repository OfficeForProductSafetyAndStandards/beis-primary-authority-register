<?php

namespace Drupal\par_flow_transition_coordinator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_flow_transition_business\Form\ParFlowTransitionAddressForm as ParFlowTransitionAddressBusinessForm;

/**
 * The primary contact form for the partnership details steps of the
 * 2nd Data Validation/Transition User Journey.
 */
class ParFlowTransitionAddressForm extends ParFlowTransitionAddressBusinessForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_coordinator_address';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataPremises $par_data_premises = NULL) {
    return parent::buildForm($form, $form_state, $par_data_partnership, $par_data_premises);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
