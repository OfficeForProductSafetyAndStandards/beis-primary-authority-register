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
   * Set the page title.
   */
  protected $pageTitle = 'Existing partnerships';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Load application type from previous step.
//    $cid = $this->getFlowNegotiator()->getFormKey('par_partnership_application_type');
//    $application_type = $this->getFlowDataHandler()->getDefaultValues('application_type', '', $cid);
//
//    if ($application_type == 'direct') {
//      $form['business_notified'] = [
//        '#type' => 'radios',
//        '#title' => $this->t('If the organisation is regulated by another local authority they must be notified that this authority will continue to regulate them?'),
//        '#options' => [
//          2 => 'The organisation is regulated by only one local authority, which is your local authority',
//          1 => 'The organisation is regulated by other local authorities and the organisation has been informed that they will still be regulating their organisation.',
//        ],
//        '#default_value' => $this->getFlowDataHandler()->getDefaultValues('business_regulated_by_one_authority', FALSE),
//      ];
//    }
//    else {
      $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->progressRoute(), $this->getRouteParams());
      return new RedirectResponse($url);
//    }

    return parent::buildForm($form, $form_state);
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
