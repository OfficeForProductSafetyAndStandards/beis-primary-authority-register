<?php

namespace Drupal\par_member_upload_flows;

use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowNegotiatorInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class ParMemberCsvHandler implements ParMemberCsvHandlerInterface {

  use LoggerChannelTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The csv file extension.
   */
  const FILE_EXTENSION = 'csv';

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
   * The flow negotiator.
   *
   * @var \Drupal\par_flows\ParFlowNegotiatorInterface
   */
  protected $negotiator;

  /**
   * The flow data manager.
   *
   * @var \Drupal\par_flows\ParFlowDataHandlerInterface
   */
  protected $flowDataHandler;

  /**
   * The mappings between columns and entities.
   *
   * The headings are defined once here (for validation)
   * and once on the self::generate() method for compilation.
   */
  protected function getMappings() {
    return [
      'Organisation name' => [
        'entity' => 'par_data_organisation',
        'field' => 'organisation_name',
      ],
      'Address Line 1' => [
        'entity' => 'par_data_premises',
        'field' => 'organisation_name',
      ],
      'Address Line 2' => NULL,
      'Address Line 3' => NULL,
      'Town' => NULL,
      'County' => NULL,
      'Postcode' => NULL,
      'Nation' => NULL,
      'First Name' => NULL,
      'Last Name' => NULL,
      'Work Phone' => NULL,
      'Mobile Phone' => NULL,
      'Email' => [
        new Length(['max' => 500]),
        new Email(),
        new NotBlank(),
      ],
      'Membership Start Date' => [
        new DateTime(['format' => 'd/m/Y']),
        new NotBlank(),
      ],
      'Membership End Date' => NULL,
      'Covered by Inspection Plan' => NULL,
      'Legal Entity Name (first)' => [
        new NotBlank(),
      ],
      'Legal Entity Type (first)' => [
        new NotBlank(),
      ],
      'Legal Entity Number (first)' => NULL,
      'Legal Entity Name (second)' => NULL,
      'Legal Entity Type (second)' => NULL,
      'Legal Entity Number (second)' => NULL,
      'Legal Entity Name (third)' => NULL,
      'Legal Entity Type (third)' => NULL,
      'Legal Entity Number (third)' => NULL,
    ];
  }

  protected function getConstraints() {
    return [
      'Organisation name' => [
        new Length(['max' => 500]),
        new NotBlank(),
      ],
      'Email' => [
        new Length(['max' => 500]),
        new Email(),
        new NotBlank(),
      ],
      'Membership Start Date' => [
        new DateTime(['format' => 'd/m/Y']),
        new NotBlank(),
      ],
      'Legal Entity Name (first)' => [
        new NotBlank(),
      ],
      'Legal Entity Type (first)' => [
        new NotBlank(),
      ],
    ];
  }

  /**
   * Constructs a ParFlowNegotiator instance.
   *
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The entity type manager.
   * @param \Drupal\par_data\ParDataManagerInterface $par_data_manager
   *   The par data manager.
   * @param \Drupal\par_flows\ParFlowNegotiatorInterface $negotiation
   *   The flow negotiator.
   * @param \Drupal\par_flows\ParFlowDataHandlerInterface $data_handler
   *   The flow data handler.
   */
  public function __construct(Serializer $serializer, ParDataManagerInterface $par_data_manager, ParFlowNegotiatorInterface $negotiator, ParFlowDataHandlerInterface $data_handler) {
    $this->seriailzer = $serializer;
    $this->parDataManager = $par_data_manager;
    $this->negotiator = $negotiator;
    $this->flowDataHandler = $data_handler;
  }

  /**
   * Get serializer.
   *
   * @return Serializer
   */
  public function getSerializer() {
    return $this->seriailzer;
  }

  /**
   * Get serializer.
   *
   * @return ParDataManagerInterface
   */
  public function getParDataManager() {
    return $this->parDataManager;
  }

  /**
   * Get the flow negotiator.
   *
   * @return ParFlowNegotiatorInterface
   */
  public function getFlowNegotiator() {
    return $this->negotiator;
  }

  /**
   * Get the flow data handler.
   *
   * @return ParFlowDataHandlerInterface
   */
  public function getFlowDataHandler() {
    return $this->flowDataHandler;
  }

  /**
   * {@inheritdoc}
   */
  public function loadFile(FileInterface $file, array &$rows = []) {
    // Need to set auto_detect_line_endings to deal with Mac line endings.
    // @see http://php.net/manual/en/function.fgetcsv.php
    ini_set('auto_detect_line_endings', TRUE);

    try {
      $csv = file_get_contents($file->getFileUri());
      $data = $this->getSerializer()->decode($csv, 'csv');
    }
    catch (UnexpectedValueException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
      return;
    }

    $rows = array_merge($rows, $data);
  }

  /**
   * Save data to a CSV file.
   *
   * @param array $rows
   *   An array to add processed rows to.
   * @param $par_data_partnership
   *   The partnership to generate the name for.
   *
   * @return bool
   */
  public function saveFile(array $rows = [], $par_data_partnership) {
    $data = $this->getSerializer()->encode($rows, 'csv');

    $directory = 's3private://member-csv/';
    $name = $par_data_partnership->generateMembershipFilename();
    $file = file_save_data($data, $directory . $name . '.' . self::FILE_EXTENSION, FILE_EXISTS_REPLACE);

    return $file;
  }

  /**
   * Batch save file helper.
   *
   * The same as self::saveFile but adapted for static context.
   *
   * @param $par_data_partnership
   */
  public static function batchSaveFile($par_data_partnership, $cid, &$context) {
    $rows = $context['results'];

    $data = \Drupal::service('serializer')->encode($rows, 'csv');
    $data_handler = \Drupal::service('par_flows.data_handler');

    $directory = 's3private://member-csv/';
    $name = $par_data_partnership->generateMembershipFilename();
    $file = file_save_data($data, $directory . $name . '.' . self::FILE_EXTENSION, FILE_EXISTS_REPLACE);

    $data_handler->setTempDataValue('memberlist_download_file', $file, $cid);
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {
    return array_keys($this->getMappings());
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
  public function validate(array $rows) {
    $errors = [];

    foreach ($rows as $index => $row) {
      // Check that all required headers are present and correct.
      if (array_keys($row) !== $this->getColumns()) {
        $errors[] = new ParCsvViolation($index+1, NULL, 'The column headings are incorrect or missing.');
      }

      $validator = Validation::createValidator();
      foreach ($this->getConstraints() as $column => $constraints) {
        if (NULL !== $constraints) {
          $violations = $validator->validate($row[$column], $constraints);

          foreach ($violations as $violation) {
            $errors[] = new ParCsvViolation($index+1, $column, $violation->getMessage());
          }
        }
      }
    }

    return !empty($errors) ? $errors : NULL;
  }

  /**
   * {@inheritdoc}
   *
   * The headings are defined once here (for compilation)
   * and once on the self::getMappings() method for validation.
   */
  public function generate(array $members) {
    $rows = [];

    sleep(3);

    foreach ($members as $index => $par_data_coordinated_business) {
      $par_data_organisation = $par_data_coordinated_business->getOrganisation(TRUE);
      $par_data_premises = $par_data_organisation->getPremises(TRUE);
      $rows[$index] = [
        'Member name' => $par_data_organisation->get('organisation_name')->getString(),
        'Legal entity name (first)' => $par_data_legal_entity ? $par_data_legal_entity->get('registered_name')->getString() : '',
        'Address line 1' => $par_data_premises ? $par_data_premises->get('address')->getString('addressLine1') : '',
        'Address line 2' => $par_data_premises ? $par_data_premises->get('address')->getString('addressLine2') : '',
        'Post Code' => $par_data_premises ? $par_data_premises->get('address')->getString('postalCode') : '',

        'Organisation name' => $par_data_organisation->get('organisation_name')->getString(),
        'Address Line 1' => NULL,
        'Address Line 2' => NULL,
        'Address Line 3' => NULL,
        'Town' => NULL,
        'County' => NULL,
        'Postcode' => NULL,
        'Nation' => NULL,
        'First Name' => NULL,
        'Last Name' => NULL,
        'Work Phone' => NULL,
        'Mobile Phone' => NULL,
        'Email' => null,
        'Membership Start Date' => null,
        'Membership End Date' => NULL,
        'Covered by Inspection Plan' => NULL,
        'Legal Entity Name (first)' => null,
        'Legal Entity Type (first)' => null,
        'Legal Entity Number (first)' => NULL,
        'Legal Entity Name (second)' => NULL,
        'Legal Entity Type (second)' => NULL,
        'Legal Entity Number (second)' => NULL,
        'Legal Entity Name (third)' => NULL,
        'Legal Entity Type (third)' => NULL,
        'Legal Entity Number (third)' => NULL,

      ];
    }

    return $rows;
  }

  /**
   * AJAX helper form generating the downloaded member file.
   *
   * @return mixed
   */
  public function download(ParDataPartnership $par_data_partnership) {
    $existing_members = $par_data_partnership->getCoordinatedMember();

    // Generate and save member list.
    $data = $this->generate($existing_members);
    $file = $this->saveFile($data, $par_data_partnership);

    // Redirect to saved file.
    if ($file) {
      return $file;
    }
  }

  /**
   * AJAX helper form generating the downloaded member file.
   *
   * @return mixed
   */
  public static function ajaxDownload(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $par_data_partnership = ParDataPartnership::load($form_state->getValue('par_data_partnership'));

    // Generate and save member list.
    $file = $csv_handler->download($par_data_partnership);

    // Redirect to saved file.
    if ($file) {
      $url = file_create_url($file->getFileUri());
      $response->addCommand(new RedirectCommand($url));
    }
    else {
      $element = [
        '#markup' => '<p class="error">There has been an error generating the member list, please contact pa@beis.gov.uk for assistance</p>',
      ];
      $renderer = \Drupal::service('renderer');
      $id = $form_state->getTriggeringElement()['#attributes']['id'];
      $response->addCommand(new AfterCommand('#'.$id, $renderer->render($element)));
    }

    // Remove all messages in the messenger bag for ajax callbacks.
    // To make sure logged errors can't interfere with the response.
////    \Drupal::service('messenger')->deleteAll();
    unset($_SESSION['messages']);

    return $response;
  }

  /**
   * Batch download helper.
   *
   * @param $par_data_partnership
   *   The partnership whose member list if being generated.
   * @param $cid
   *   The cache id that any output needs to be saved to.
   */
  public function batchGenerate($par_data_partnership, $cid) {
    $csv_handler_class = (new \ReflectionClass($this))->getName();

    // Configure the batch.
    $batch = [
      'title' => t('Generate Members List CSV'),
      'operations' => [],
      'init_message' => t('Member list is backing up.'),
      'progress_message' => t('Processed @current00 out of @total00.'),
      'error_message' => t('Member list backup has encountered an error.'),
    ];

    // 1. Generate a list of all members.
    $members = $par_data_partnership->getCoordinatedMember();
    $chunks = !empty($members) ? array_chunk($members, 100) : [];
    foreach ($chunks as $chunk) {
      $batch['operations'][] = [
        [$csv_handler_class, 'generate'],
        [$members]
      ];
    }

    // 2: Save the list of members.
    $batch['operations'][] = [
      [$csv_handler_class, 'batchSaveFile'],
      [$par_data_partnership, $cid]
    ];

    batch_set($batch);
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
