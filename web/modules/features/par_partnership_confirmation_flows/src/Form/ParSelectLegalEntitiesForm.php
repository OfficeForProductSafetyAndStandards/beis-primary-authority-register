<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The primary contact form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParSelectLegalEntitiesForm extends ParBaseForm {

  protected $pageTitle = 'Choose the legal entities for the partnership';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_select_legal_entities';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $selected_legal_entities = $form_state->getValue('field_legal_entity');

    // Check if at least one legal entity has been selected.
    if (!array_filter($selected_legal_entities)) {
      $this->setElementError('field_legal_entity', $form_state, 'Please select at least one legal entity.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

//    if ($legal_entity->save()) {
//      $this->getFlowDataHandler()->deleteStore();
//    }
//    else {
//      $message = $this->t('This %field could not be saved for %form_id');
//      $replacements = [
//        '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
//        '%form_id' => $this->getFormId(),
//      ];
//      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
//    }

  }

}
