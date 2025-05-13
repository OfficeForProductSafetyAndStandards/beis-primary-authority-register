<?php

namespace Drupal\par_transfer_partnerships_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_flows\Form\ParBaseForm;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form for managing regulatory functions.
 */
class ParManageFunctionsForm extends ParBaseForm {

  protected $pageTitle = 'Manage regulatory functions';

  /**
   * Load the data for this form.
   */
  #[\Override]
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state, ?ParDataAuthority $par_data_authority = NULL) {
    // This form isn't yet required, the initial functionality doesn't handle
    // situations where there is a mismatch of regulatory functions between
    // the authorities.
    // @todo introduce functionality to handle mismatched regulatory functions.
    $url = $this->getFlowNegotiator()->getFlow()->progress();
    return new RedirectResponse($url->toString());

    // Make sure to add the authority cacheability data to this form.
    $this->addCacheableDependency($par_data_authority);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('remove_reason')) {
      $id = $this->getElementId('remove_reason', $form);
      $form_state->setErrorByName($this->getElementName(['confirm']), $this->wrapErrorMessage('Please enter the reason you are removing this inspection plan.', $id));
    }
  }

  /**
   * {@inheritdoc}
   */
  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');
  }

}
