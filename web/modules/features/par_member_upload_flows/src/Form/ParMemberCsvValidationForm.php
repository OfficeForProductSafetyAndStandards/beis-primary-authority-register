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
class ParMemberCsvValidationForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * Set the page title.
   */
  protected $pageTitle = 'CSV validation errors';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_member_upload_csv_validate';
  }

  /**
   * @return ParMemberCsvHandlerInterface
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    // Load csv data from temporary data storage and display any errors or move on.
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $rows = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    $errors = $this->getCsvHandler()->validate($rows);

    if (!empty($rows) && empty($errors)) {
      return $this->redirect($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
    }

    // Display error message if violations are found in the uploaded csv file.
    $form['error_list'] = [
      '#theme' => 'table',
      '#attributes' => ['class' => ['form-group']],
      '#title' => 'CSV Errors',
      '#header' => [
        'Line',
        'Column',
        'Error',
      ],
      '#empty' => $this->t("No members were found in the CSV file."),
    ];

    foreach ($errors as $index => $error) {
      $form['error_list']['#rows'][] = [
        'data' => [
          'line' => $error->getLine(),
          'column' => $error->getColumn(),
          'error' => $error->getMessage(),
        ],
      ];
    }

    $form = parent::buildForm($form, $form_state);

    if ($this->getFlowNegotiator()->getFlow()->hasAction('done')) {
      $form['actions']['done']['#value'] = 'Re-upload';
    }
    return $form;
  }

}
