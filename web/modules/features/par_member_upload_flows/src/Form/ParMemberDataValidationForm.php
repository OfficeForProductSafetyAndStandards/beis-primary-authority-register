<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\FileInterface;
use Drupal\file\Entity\File;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberUploadFlowsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    // Load csv data from temporary data storage and display any errors or move on.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $errors = $this->getFlowDataHandler()->getTempDataValue('errors', $cid);

    if (empty($errors)) {
      
      $this->submitForm($form, $form_state);
    }

    // @TODO SHOW ERRORS HERE.

    $form = parent::buildForm($form, $form_state);

    // Change the button text
  }

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

}
