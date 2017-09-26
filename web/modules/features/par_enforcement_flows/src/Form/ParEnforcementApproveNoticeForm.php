<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_approve';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues() {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    $this->retrieveEditableValues();

    $form['authority'] = $this->renderSection('Notification of Enforcement action from', $par_data_enforcement_notice, ['field_enforcing_authority' => 'title']);

    if (!$par_data_enforcement_notice->get('field_legal_entity')->isEmpty()) {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['field_legal_entity' => 'title']);

      // @TODO If there is only one organisation for this legal entity
      // we can potentially display the address, but otherwise we
      // can only display the name.
    }
    else {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['legal_entity_name' => 'summary']);
    }

    // Show details of each action.
    if (!$par_data_enforcement_notice->get('field_enforcement_action')->isEmpty()) {
      $form['actions'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
    }
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form['actions'][$delta] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['actions'][$delta]['title'] = $this->renderSection('Title of action', $action, ['title' => 'title']);

      $form['actions'][$delta]['regulatory_function'] = $this->renderSection('Regulatory function', $action, ['field_regulatory_function' => 'title']);

      $form['actions'][$delta]['details'] = $this->renderSection('Details', $action, ['details' => 'full']);
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
