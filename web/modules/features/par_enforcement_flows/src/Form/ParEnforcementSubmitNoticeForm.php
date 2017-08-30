<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementSubmitNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'raise_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_raise_confirm';
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->retrieveEditableValues();

    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit & Next'),
    ];

    $cancel_link = $this->getFlow()->getNextLinkByOperation('cancel')->setText('Cancel')->toString();
    $form['cancel'] = [
      '#type' => 'markup',
      '#markup' => t('@link', ['@link' => $cancel_link]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
