<?php

namespace Drupal\par_flow_transition_coordinator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flow_transition_business\Form\ParFlowTransitionLegalEntityForm as ParFlowTransitionLegalEntityBusinessForm;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParFlowTransitionLegalEntityForm extends ParFlowTransitionLegalEntityBusinessForm {

  /**
   * @var string
   *   A machine safe value representing the current form journey.
   */
  protected $flow = 'transition_coordinator';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_flow_transition_coordinator_legal_entity';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataLegalEntity $par_data_legal_entity = NULL) {
    $form =  parent::buildForm($form, $form_state, $par_data_partnership, $par_data_legal_entity);

    // Change labels for coordinator journey.
    if (!empty($par_data_legal_entity)) {
      $id = $par_data_legal_entity->id();
      $form['intro'] = [
        '#markup' => $this->t('Change the legal entity of your association'),
      ];
    }
    else {
      $form['intro'] = [
        '#markup' => $this->t('Add a legal entity for your association'),
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
