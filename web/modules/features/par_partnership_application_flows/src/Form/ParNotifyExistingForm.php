<?php

namespace Drupal\par_partnership_application_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_application_flows\ParFlowAccessTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The condition acceptance form for partnership applications.
 */
class ParNotifyExistingForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Sets the page title.
   *
   * @var pageTitle
   */
  protected $pageTitle = 'Existing partnerships';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $url = $this->getFlowNegotiator()->getFlow()->progress();
    return new RedirectResponse($url->toString());
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Load application type from previous step.
    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
    $application_type = $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid);

    if ($application_type == 'direct') {
      // Check if an empty value is provided.
      if ($form_state->getValue('business_notified') === FALSE) {
        $id = $this->getElementId('business_notified', $form);
        $form_state->setErrorByName($this->getElementName(['business_notified']), $this->wrapErrorMessage('Please confirm whether the organisation has been notified that any existing local authorities will continue to regulate it.', $id));
      }
    }
  }

}
