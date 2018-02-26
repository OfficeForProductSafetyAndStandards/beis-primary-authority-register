<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberDataValidationForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_upload_csv_validate';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Define array variable.
    $violations = [];

    // Load csv data from temporary data storage and display any errors or move on.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $violations = $this->getFlowDataHandler()->getTempDataValue('errors', $cid);

    // Display error message if violations are found in the uploaded csv file.
    if (count($violations) > 0) {
      // Prepare error message variable.
      $error = 'We found the following errors: <br><br>';
      $error .= implode('<br>', $violations);
      $error .= '<br><br>Please check and try again.';

      // Display error to the end user.
      $form['message'] = [
        '#markup' => t($error),
      ];
    }

    // Redirect to the next step if no violations are found.
    if (count($violations) <= 0) {
      $redirect_url = str_replace('validate', 'confirm', \Drupal::service('path.current')->getPath());
      $response = new RedirectResponse($redirect_url);
      $response->send();
    }

    return parent::buildForm($form, $form_state);
  }

}
