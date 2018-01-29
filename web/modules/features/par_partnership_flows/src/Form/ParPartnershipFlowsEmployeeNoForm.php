<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsEmployeeNoForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  protected $pageTitle = 'Edit number of employees';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_employee_number';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we want to use values already saved we have to tell
      // the form about them.
      $par_data_organisation = current($par_data_partnership->getOrganisation());

      $this->getFlowDataHandler()->setFormPermValue('employees_band', $par_data_organisation->get('employees_band')->getString());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    $organisation_bundle = $this->getParDataManager()->getParBundleEntity('par_data_organisation');

    // Business details.
    $form['employees_band'] = [
      '#type' => 'select',
      '#title' => $this->t('Number of employees'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('employees_band'),
      '#options' => $organisation_bundle->getAllowedValues('employees_band'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
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
