<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
//use Drupal\file\FileInterface;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;
use Drupal\par_member_upload_flows\ParMemberCsvHandlerInterace;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
//use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder;

/**
 * The upload CSV form for importing partnerships.
 */
class ParMemberUploadFlowsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Multiple file field.
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload a list of members'),
      '#description' => t('Upload your CSV file, be sure to make sure the'
        . ' information is accurate so that it can all be processed'),
      '#upload_location' => 's3private://member-csv/',
      '#multiple' => FALSE,
      '#required' => TRUE,
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues('csv'),
      '#upload_validators' => [
        'file_validate_extensions' => [
          0 => 'csv',
        ]
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @return ParMemberCsvHandlerInterace
   */
  public function getCsvHandler() {
    return \Drupal::service('par_member_upload_flows.csv_handler');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Process uploaded csv file.
    if ($csv = $this->getFlowDataHandler()->getTempDataValue('csv')) {
      // Define rows array.
      $rows = [];

      // Load the submitted file and process the data.
      $files = File::loadMultiple($csv);
      foreach ($files as $file) {
        $rows = $this->getCsvHandler()->loadFile($file, $rows);
      }



      //      $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
      $serializer = new Serializer([new CsvEncoder()]);

      // instantiation, when using it inside the Symfony framework.
//      $serializer = $container->get('serializer');
//      $serializer = $serializer->get('serializer');
//
//      // encoding contents in CSV format.
//      $serializer->encode($data, 'csv');
//
      // decoding CSV contents.
      $file_path = 's3private://member-csv/coordinated_member_upload_template_10000_5.csv';
      $serializer->encode($file_path, 'csv');
//      $data = $serializer->decode(file_get_contents($file_path), 'csv');
//      dpm($data);
//
//      foreach ($files as $file) {
//        // Save processed row data in an array.
//        $rows[] = $this->getParDataManager()->processCsvFile($file, $rows);
//      }
//
//      // Save the data in the User's temp private store for later processing.
//      if (!empty($rows)) {
//
//        // Set csv data in temporary data storage.
//        $this->getFlowDataHandler()->setTempDataValue('coordinated_members', $rows);
//      }
    }
  }

}
