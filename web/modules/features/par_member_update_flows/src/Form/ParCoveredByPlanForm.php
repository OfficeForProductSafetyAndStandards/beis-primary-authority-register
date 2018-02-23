<?php

namespace Drupal\par_member_update_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_update_flows\ParFlowAccessTrait;

/**
 * The form for marking whether a member is covered by an inspection plan.
 */
class ParCoveredByPlanForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = "Inspection plan coverage";

  /**
   * {@inheritdoc}
   */
  public function loadData() {
    $par_data_coordinated_business = $this->getFlowDataHandler()
      ->getParameter('par_data_coordinated_business');

    parent::loadData();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_coordinated_business = $this->getFlowDataHandler()
      ->getParameter('par_data_coordinated_business');

    $par_data_coordinated_business->set('covered_by_inspection',
      $this->getFlowDataHandler()
        ->getTempDataValue('covered_by_inspection'));

    // Commit changes.
    if ($par_data_coordinated_business->save()) {
      $this->getFlowDataHandler()->deleteStore();
    }
    else {
      $message = $this->t('Field %field could not be saved for %form_id');
      $replacements = [
        '%field' => 'covered_by_inspection',
        '%form_id' => $this->getFormId(),
      ];
      $this->getLogger($this->getLoggerChannel())
        ->error($message, $replacements);
    }

  }

}
