<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_confirmation_flows\ParPartnershipFlowsTrait;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParEmployeeNumberForm extends ParBaseForm {

  /**
   * Set the page title.
   */
  protected $pageTitle = "Confirm number of employees";

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_confirmation_employee_number';
  }

  /**
   * Load the data for this form.
   */
  public function loadData() {
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = $partnership ? $partnership->getOrganisation(TRUE) : NULL;

    // For the apply journey we will always edit the first value.
    $this->getflowDataHandler()->setParameter('par_data_organisation', $par_data_organisation);

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getflowDataHandler()->getParameter('par_data_partnership');
    $par_data_organisation = current($partnership->getOrganisation());
    $par_data_organisation->set('employees_band', $this->getFlowDataHandler()->getTempDataValue('employees_band'));
    if ($par_data_organisation->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('The %field field could not be saved for %form_id');
      $replacements = [
        '%field' => 'size',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
    }
  }

}
