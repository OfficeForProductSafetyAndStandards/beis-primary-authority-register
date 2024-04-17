<?php

namespace Drupal\par_enforcement_remove_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_forms\ParFormPluginInterface;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementRemovalForm extends ParBaseForm {

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = "Notice of enforcement actions | Remove";

  /**
   * Load the data for this.
   */
  public function loadData() {
    $par_data_enforcement_notice = $this->getFlowDataHandler()->getParameter('par_data_enforcement_notice');

    if ($par_data_enforcement_notice && $par_data_enforcement_actions = $par_data_enforcement_notice->getEnforcementActions()) {
      $this->getFlowDataHandler()->setParameter('par_data_enforcement_actions', $par_data_enforcement_actions);

      // In order to display multiple cardinality the enforcement_action_review
      // plugin needs to know how many instances of data to display, it doesn't
      // use this data other than to know how many instances of data to display.
      // The actual displayed data comes from the par_data_enforcement_actions
      // parameter set above.
      $action_detail_component = $this->getComponent('enforcement_action_review');
      if ($action_detail_component instanceof ParFormPluginInterface) {
        $values = [];
        foreach ($par_data_enforcement_actions as $action) {
          $values[] = ['action_title' => $action->label()];
        }
        $this->getFlowDataHandler()->setPluginTempData($action_detail_component, $values);
      }
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
