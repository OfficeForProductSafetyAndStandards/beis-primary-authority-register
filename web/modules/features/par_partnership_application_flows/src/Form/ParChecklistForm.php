<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;

/**
 * The checklist form for partnership applications.
 */
class ParChecklistForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $applicationType = ucfirst($this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid));

    // Set page title.
    $this->pageTitle = "Declaration for a {$applicationType} partnership application";

    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $application_type = $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid);

    // Get Primary Authority Terms and Conditions URL.
    $terms_page = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/49');

    switch ($application_type) {
      case 'direct':
        $checklist = [
          'the organisation is eligible to enter into a partnership',
          'your local authority is suitable for nomination as primary authority for the organisation',
          'a written summary of partnership arrangements has been agreed with the organisation',
          t("your local authority agrees to the <a href='{$terms_page}' target='_blank'>terms and conditions (opens in a new window)</a>"),
        ];

        break;

      case 'coordinated':
        $checklist = [
          'the prospective co-ordinator partner is suitable for nomination as a co-ordinator',
          'your local authority is suitable for nomination as primary authority partner for the co-ordinator',
          'a written summary of partnership arrangements has been agreed with the co-ordinator',
          t("your local authority agrees to the <a href='{$terms_page}' target='_blank'>terms and conditions (opens in a new window)</a>"),
        ];

        break;

    }

    if (!empty($checklist)) {
      $form['checklist'] = [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#title' => 'Please confirm that',
        '#items' => $checklist,
        '#attributes' => ['class' => ['list', 'list-bullet']],
        '#wrapper_attributes' => ['class' => ['form-group']],
      ];

      $form['confirm'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I confirm these conditions have been met'),
        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('confirm', FALSE),
        '#return_value' => 'on',
      ];

      $form['help'] = [
        '#type' => 'markup',
        '#markup' => '<p>These essential conditions for the nomination of direct partnerships are required by the Regulatory Enforcement and Sanctions Act 2008 (as amended by the Enterprise Act 2016) and the Primary Authority Statutory Guidance.</p>',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('confirm')) {
      $id = $this->getElementId('confirm', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please confirm that all conditions for a new partnership have been met.', $id));
    }
  }

}
