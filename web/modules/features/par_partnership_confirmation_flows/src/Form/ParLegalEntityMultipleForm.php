<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * Add multiple legal entities forms.
 */
class ParLegalEntityMultipleForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  protected $pageTitle = 'Add a legal entity for the organisation';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_legal_entity_add_multiple';
  }

  public function multipleItemActionsSubmit(array &$form, FormStateInterface $form_state) {
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $this->getFlowDataHandler()->setFormTempData($values);

    // Get value of current amount of fields displayed.
    $fields_to_display = $this->getFlowDataHandler()
      ->getTempDataValue('fields_to_display');

    $submit_action = $form_state->getTriggeringElement()['#name'];

    // Check input button name to decide whether to add/remove a field.
    $submit_action === 'add_another' ?
      $fields_to_display++ : $fields_to_display--;

    // Populate hidden field to generate more legal entity form elements.
    $this->getFlowDataHandler()
      ->setTempDataValue('fields_to_display', $fields_to_display);
  }

}
