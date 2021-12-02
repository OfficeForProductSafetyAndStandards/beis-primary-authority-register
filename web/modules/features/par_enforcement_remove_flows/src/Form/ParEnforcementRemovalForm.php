<?php

namespace Drupal\par_enforcement_remove_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\ParDataException;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormBuilder;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementRemovalForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = "Notice of enforcement actions | Remove";

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);
      $this->getFlowDataHandler()->setTempDataValue(ParFormBuilder::PAR_COMPONENT_PREFIX . 'enforcement_action_review', $par_data_enforcement_actions);
    }

    parent::loadData();


  }

  /**
   * Manipulate the reason for removal into string format.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $reason_selection = $this->getFlowDataHandler()->getTempDataValue('reason_selection');

    // Set the readable value for the reason selection.
    if ($reason_selection) {
      $reason_options = $this->getComponent('removal_reason')->getConfiguration()['reasons'] ?? [];
      $form_state->setValue('reason', $reason_options[$reason_selection]);
    }

    parent::submitForm($form, $form_state);
  }
}
