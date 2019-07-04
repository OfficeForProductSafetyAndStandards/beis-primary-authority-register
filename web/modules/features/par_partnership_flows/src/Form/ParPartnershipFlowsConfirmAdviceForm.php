<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;
use Drupal\par_partnership_flows\ParPartnershipFlowAccessTrait;

/**
 * Security warning guidelines for advice documents.
 */
class ParPartnershipFlowsConfirmAdviceForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use ParPartnershipFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    // Set page title.
    $this->pageTitle = "Uploading advice documents declaration";
    return parent::titleCallback();
  }

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    $form['declaration'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I confirm the documents to be uploaded do not contain signatures or sensitive information.'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('confirm', FALSE),
      '#return_value' => 'on',
    ];

    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<p>Please remove all signatures and sensitive information before uploading advice documents.</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('declaration')) {
      $id = $this->getElementId('declaration', $form);
      $form_state->setErrorByName($this->getElementName(['declaration']), $this->wrapErrorMessage('Please confirm the security guidelines have been applied.', $id));
    }
  }

}
