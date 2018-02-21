<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\FileInterface;
use Drupal\file\Entity\File;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\user\Entity\User;
use Symfony\Component\Serializer\Serializer;
//use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
//use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterace;

class ParMemberCsvHandler implements ParMemberCsvHandlerInterface {

  /**
   * The symfony serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $seriailzer;

  /**
   * The PAR data manager for acting upon PAR Data.
   *
   * @var \Drupal\par_data\ParDataManagerInterface
   */
  protected $parDataManager;

  /**
   * Constructs a ParFlowNegotiator instance.
   *
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route
   *   The entity bundle info service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The entity bundle info service.
   */
  public function __construct(Serializer $serializer, ParDataManagerInterface $par_data_manager) {
    $this->entityTypeManager = $serializer;
    $this->parDataManager = $par_data_manager;
  }

  /**
   * Process a CSV file
   *
   * @param FileInterface $file
   * @param array $rows
   *   An array to add processed rows to.
   * @param boolean $skip
   *   Whether to skip the headers.
   *
   * @return array
   *   An array of row data.
   */
  public function loadFile(FileInterface $file, array $rows = [], bool $skip = TRUE) {
    // Need to set auto_detect_line_endings to deal with Mac line endings.
    // @see http://php.net/manual/en/function.fgetcsv.php
    ini_set('auto_detect_line_endings', TRUE);

    if (($handle = fopen($file->getFileUri(), "r")) !== FALSE) {
      while (($data = fgetcsv($handle)) !== FALSE) {
        if ($data !== NULL && !$skip) {
          $rows[] = $data;
        }
        $skip = FALSE;
      }
      fclose($handle);
    }

    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {

  }

  /**
   * {@inheritdoc}
   */
  public function getColumnsByIndex(int $index) {

  }

  /**
   * {@inheritdoc}
   */
  public function lock() {

  }

  /**
   * {@inheritdoc}
   */
  public function unlock() {

  }

  /**
   * {@inheritdoc}
   */
  public function validateRow(array $csv = []) {

    // Initialise default value.
    $rows = [];
    $row_number = 0;
    $error_message = [];
    $error = '';

    // Load the submitted file and process the data.
    /** @var $files FileInterface[] * */
    $files = File::loadMultiple($csv);

    // Loop through each csv file row and process.
    foreach ($files as $file) {
      // csv file row data .
      $rows = $this->getCsvHandler()->loadFile($file, $rows);
    }

    // Save the data in the User's temp private store for later processing.
    if (!empty($rows)) {
      // Set csv data in temporary data storage.
      $this->getFlowDataHandler()->setTempDataValue('coordinated_members', $rows);
    }

    // Form cache id.
    //ParControllerTrait
//    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');

    // Load csv data from temporary data storage and assign to a variable.
    $csv_data = $this->getFlowDataHandler()->getTempDataValue('coordinated_members', $cid);

    // Loop through all csv rows and process data.
    foreach ($csv_data as $key => $value) {
      // Set row number.
      $row_number = $key + 2;

      // CSV data validation - File format OK, all columns present, but
      // missing required field(s).
      if (!par_member_upload_flows_required_fields($value, $row_number) == NULL) {
        $error_message[] = par_member_upload_flows_required_fields($value, $row_number);
      }
    }

    // If there is an error message, then process following.
    if (count($error_message) > 0) {
      // Prepare error message variable.
      $error = 'We found the following errors: <br><br>';
      $error .= implode('<br>', $error_message);
      $error .= '<br><br>Please check and try again.';

      // Display error to the end user.
      drupal_set_message(t('@error', ['@error' => $error]), 'error');
      dpm($error);
    }

    return 'Validation in progress...';
  }

  /**
   * {@inheritdoc}
   */
  public function process() {

  }

  /**
   * {@inheritdoc}
   */
  public function cleanup() {

  }

  /**
   * {@inheritdoc}
   */
  public function complete() {

  }

}
