<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * Organisation Legal Entities selection form.
 * This generates a list of legal entities stored on the PAR Organisation as
 * checkboxes.
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Filter form field data to those specifically checked.
    $selected_legal_entities = array_filter($this->getFlowDataHandler()->getTempDataValue('field_legal_entity'));

    // Update legal entities on the partnership with those selected on the form.
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_partnership->set('field_legal_entity', $selected_legal_entities);

    // Commit changes to partnership entity.
    if ($par_data_partnership->save()) {
      // Delete form data in favour for querying the partnership entity.
      $this->getFlowDataHandler()->deleteFormTempData();
    }
    else {
      $message = $this->t('This %field could not be saved for %form_id');
      $replacements = [
        '%field' => $this->getFlowDataHandler()->getTempDataValue('registered_name'),
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }

  }

}
