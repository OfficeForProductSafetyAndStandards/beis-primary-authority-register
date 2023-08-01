<?php

namespace Drupal\par_partnership_amend_flows\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The form for setting a transfer date.
 */
class ParTransferDateForm extends ParBaseForm {

  protected $pageTitle = 'When would you like this transfer to take place?';

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataAuthority $par_data_authority = NULL) {
    // Make sure to add the person cacheability data to this form.
    $this->addCacheableDependency($par_data_authority);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Get the current date.
    $request_time = \Drupal::time()->getRequestTime();
    $now = DrupalDateTime::createFromTimestamp($request_time);

    $date_value = $this->getFlowDataHandler()->getTempDataValue('date');
    $date = $date_value ? DrupalDateTime::createFromFormat('Y-m-d', $date_value, ['validate_format' => FALSE]) : NULL;
    if ($date > $now) {
      $id_key = $this->getElementKey('date', 1, TRUE);
      $message = $this->t("The date cannot be in the future.")->render();
      $form_state->setErrorByName('date', $this->wrapErrorMessage($message, $this->getElementId($id_key, $form)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
    $par_data_inspection_plan = $this->getFlowDataHandler()->getParameter('par_data_inspection_plan');
    $delta = $this->getFlowDataHandler()->getTempDataValue('delta');
  }

}
