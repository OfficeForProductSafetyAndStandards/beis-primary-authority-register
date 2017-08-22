<?php

namespace Drupal\par_flow_transition_coordinator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Form\ParFlowTransitionTradingForm as ParFlowTransitionTradingBusinessForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionTradingForm extends ParFlowTransitionTradingBusinessForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_coordinator_trading';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, $trading_name_delta = NULL) {
    $form = parent::buildForm($form, $form_state, $par_data_partnership, $trading_name_delta);

    // Change labels for coordinator journey.
    if (empty($trading_name_delta)) {
      $form['intro'] = [
        '#markup' => $this->t('Add another trading name for your organisation'),
      ];

    }
    else {
      $form['intro'] = [
        '#markup' => $this->t('Change the trading name of your organisation'),
      ];
    }
    $form['trading_name']['#description'] = $this->t('Sometimes companies trade under a different name to their registered, legal name. This is known as a \'trading name\'. State any trading names used by the association.');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
