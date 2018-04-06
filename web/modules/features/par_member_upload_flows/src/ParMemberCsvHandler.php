<?php

namespace Drupal\par_member_upload_flows;

use CommerceGuys\Intl\Exception\UnknownCountryException;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataCoordinatedBusiness;
use Drupal\par_data\Entity\ParDataCoordinatedBusinessType;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataLegalEntityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\Entity\ParDataPremisesType;
use Drupal\par_data\ParDataException;
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
   * The maximum number of rows that can be processed.
   */
  const MAX_ROW_LIMIT = 10000;

  /**
   * The maximum number of rows that can be processed per batch run.
   */
  const BATCH_LIMIT = 100;

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

  public function getMappings() {
    return [
      'partnership_id' => 'Partnership id',
      'organisation_name' => 'Organisation name',
      'address_line_1' => 'Address Line 1',
      'address_line_2' => 'Address Line 2',
      'town' => 'Town',
      'county' => 'County',
      'postcode' => 'Postcode',
      'country_code' => 'Country Code',
      'nation' => 'Nation',
      'first_name' => 'First Name',
      'last_name' => 'Last Name',
      'work_phone' => 'Work Phone',
      'mobile_phone' => 'Mobile Phone',
      'email' => 'Email',
      'membership_start' => 'Membership Start Date',
      'membership_end' => 'Membership End Date',
      'ceased' => 'Membership ceased',
      'covered' => 'Covered by Inspection Plan',
      'legal_entity_name_first' => 'Legal Entity Name (first)',
      'legal_entity_number_first' => 'Legal Entity Type (first)',
      'legal_entity_type_first' => 'Legal Entity Number (first)',
      'legal_entity_name_second' => 'Legal Entity Name (second)',
      'legal_entity_number_second' => 'Legal Entity Type (second)',
      'legal_entity_type_second' => 'Legal Entity Number (second)',
      'legal_entity_name_third' => 'Legal Entity Name (third)',
      'legal_entity_number_third' => 'Legal Entity Type (third)',
      'legal_entity_type_third' => 'Legal Entity Number (third)',
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

  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * Allows non-static methods to be called statically from within the batch.
   *
   * @param $method
   *   The static method being called.
   * @param $args
   *   The arguments to be passed to the called method
   */
  public static function __callStatic($method, $args) {
    if (strpos($method, 'batch__') === 0) {
      $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');

      sleep(3);

      try {
        $non_static_method = str_replace('batch__', '', $method);
        if ($result = $csv_handler->{$non_static_method}(...$args)) {
          $args['context']['results'] = $result;
        }
      }
      catch (ParCsvProcessingException $exception) {
        $csv_handler->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
        $args['context']['success'] = FALSE;
      }
    }
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
   * Get the country repository from the address module.
   */
  public function getCountryRepository() {
    return \Drupal::service('address.country_repository');
  }

  public function getMapping($key) {
    $mappings = $this->getMappings();
    return isset($mappings[$key]) ? $mappings[$key] : NULL;
  }

  public function getMappingByHeading($heading) {
    $mappings = $this->getMappings();
    $key = array_search($heading, $mappings, TRUE);
    return $key !== FALSE ? $key : NULL;
  }

  public function getValue($row, $key, $default = NULL) {
    $column = $this->getMapping($key);
    return $column && isset($row[$column]) ? $row[$column] : $default;
  }

  protected function getConstraints() {
    /** @var ParDataPremisesType $par_data_premises_type */
    $par_data_premises_type = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $country_options = $this->getCountryRepository()->getList(NULL) + $par_data_premises_type->getAllowedValues('nation');
    $boolean_options = ['Yes', 'No', 'Y', 'N'];
    return [
      'organisation_name' => [
        new Length(['max' => 500]),
        new NotBlank(),
      ],
      'email' => [
        new Length(['max' => 500]),
        new Email(),
        new NotBlank(),
      ],
      'membership_start' => [
        new DateTime(['format' => 'd/m/Y']),
        new NotBlank(),
      ],
      'legal_entity_name_first' => [
        new NotBlank(),
      ],
      'address_line_1' => [
        new NotBlank(),
      ],
      'nation' => [
        new NotBlank(),
        new Choice([
          'choices' => array_map('strtolower', $country_options),
        ]),
      ],
      'covered' => [
        new Choice([
          'choices' => array_map('strtolower', $boolean_options),
        ]),
      ],
      'legal_entity_type_first' => [
        new NotBlank(),
      ],
    ];
  }

  /**
   * Extract the individual entities from the member.
   */
  public function extract(ParDataCoordinatedBusiness $member) {
    $organisation = $member->getOrganisation(TRUE);
    $premises = $organisation ? $organisation->getPremises(TRUE) : NULL;
    $person = $organisation ? $organisation->getPerson(TRUE) : NULL;
    $legal_entities = $organisation ? $organisation->getLegalEntity() : NULL;

    return [
      'par_data_coordinated_business' => $member,
      'par_data_organisation' => $organisation ?: ParDataOrganisation::create(),
      'par_data_premises' => $premises ?: ParDataPremises::create(),
      'par_data_person' => $person ?: ParDataPerson::create(),
      'par_data_legal_entity' => $legal_entities ?: [ParDataLegalEntity::create()],
    ];
  }

  public function transform($row) {
    $data = [];

    foreach ($row as $column => $value) {
      // Don't process empty values.
      if (empty($row[$column])) {
        continue;
      }

      $key = $this->getMappingByHeading($column);
      switch ($key) {
          case 'partnership_id':
            $par_data_partnership = ParDataPartnership::load($value);
            if ($par_data_partnership) {
              $data[$column] = $par_data_partnership;
            }
            break;

          case 'nation':
            $country_codes = $this->getCountryRepository()->getList();
            $country_key = array_search($value, $country_codes);

            /** @var ParDataPremisesType $par_data_premises_type */
            $par_data_premises_type = $this->getParDataManager()->getParBundleEntity('par_data_premises');

            // If the nation is a country of the UK.
            if ($system_value = $par_data_premises_type->getAllowedValueBylabel('nation', $value, TRUE)) {
              $data[$column] = $system_value;
              $data[$this->getMapping('country_code')] = 'GB';
            }
            // If the nation is an international country.
            elseif ($country_key !== FALSE) {
              $data[$this->getMapping('country_code')] = $country_key;
            }
            break;

          case 'email':
            $data[$column] = strtolower($value);
            break;

          case 'membership_start':
            try {
              $date = DrupalDateTime::createFromFormat('d/m/Y', $value, NULL, ['validate_format' => FALSE]);
              $data[$column] = $date->format('Y-m-d');
            }
            catch (\Exception $e) {

            }

            break;

          case 'membership_end':
            try {
              $date = DrupalDateTime::createFromFormat('d/m/Y', $value, NULL, ['validate_format' => FALSE]);
              $data[$column] = $date->format('Y-m-d');
            }
            catch (\Exception $e) {

            }

            $data[$this->getMapping('ceased')] = TRUE;
            break;

          case 'covered':
            $data[$column] = strtolower($value) === 'no' || strtolower($value) === 'n' ? FALSE : TRUE;
            break;

          case 'legal_entity_type_first':
          case 'legal_entity_type_second':
          case 'legal_entity_type_third':
            /** @var ParDataLegalEntityType $par_data_legal_entity_type */
            $par_data_legal_entity_type = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

            // If user entered value matches an allowed .
            if ($system_value = $par_data_legal_entity_type->getAllowedValueBylabel('legal_entity_type', $value, TRUE)) {
              $data[$column] = $system_value;
            }
            break;

          default:
            $data[$column] = $value;
      }
    }

    return $data;
  }

  /**
   * Normalize the row of data into an entity.
   *
   * @param array $row
   *   The row of data to build the entity from.
   *
   * @return []
   *   The generated member entities.
   */
  public function normalize(array $row) {
    // We need to get the bundle information to transform bundles into labels.
    /** @var ParDataCoordinatedBusinessType $par_data_coordinated_business_type */
    /** @var ParDataOrganisationType $par_data_organisation_type */
    /** @var ParDataPremisesType $par_data_premises_type */
    /** @var ParDataPersonType $par_data_person_type */
    /** @var ParDataLegalEntityType $par_data_legal_entity_type */
    $par_data_coordinated_business_type = $this->getParDataManager()->getParBundleEntity('par_data_coordinated_business');
    $par_data_organisation_type = $this->getParDataManager()->getParBundleEntity('par_data_organisation');
    $par_data_premises_type = $this->getParDataManager()->getParBundleEntity('par_data_premises');
    $par_data_person_type = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $par_data_legal_entity_type = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    // Search this partnership for a similar member.
    $partnership_id = $this->getValue($row, 'partnership_id');
    if ($partnership_id) {
      // Transform all the columns from user input to system values.
      $data = $this->transform($row);

      $conditions = [
        'matching' => [
          'AND' => [
            ['field_organisation.entity.organisation_name', $this->getValue($data, 'organisation_name'), '='],
//            ['field_organisation.entity.field_legal_entity.entity.registered_name', 'Test Legal Entity', '='],
          ]
        ],
      ];
      $matching_members = $this->getParDataManager()
        ->getEntitiesByQuery('par_data_coordinated_business', $conditions);

      if ($matching_members) {
        foreach ($matching_members as $member) {
          // Only use this member if the partnership id is the same.
          $partnerships = $member->getPartnership();
          if (!isset($partnerships[$partnership_id])) {
            continue;
          }

          // Only use this member if the first legal entity name is the same.
          $organisation = $member->getOrganisation(TRUE);
          $legal_entity = $organisation ? $organisation->getLegalEntity(TRUE) : NULL;
          if (!$legal_entity
            || ($this->getValue($data, 'legal_entity_name_first')
              && $legal_entity->get('registered_name')->getString() === $this->getValue($data, 'legal_entity_name_first'))
          ) {
            continue;
          }

          $normalized = $this->extract($member);
          break;
        }
      }
    }

    // If no coordinated member was found create a new one.
    if (!isset($normalized)) {
      $normalized = [
        'par_data_coordinated_business' => ParDataCoordinatedBusiness::create(),
        'par_data_organisation' => ParDataOrganisation::create(),
        'par_data_premises' => ParDataPremises::create(),
        'par_data_person' => ParDataPerson::create(),
        'par_data_legal_entity' => [
          ParDataLegalEntity::create(),
        ],
      ];
      // Secondary legal entities are optional.
      if ($this->getValue($data, 'legal_entity_name_second')) {
        $normalized['par_data_legal_entity'][] = ParDataLegalEntity::create();
      }
      if ($this->getValue($data, 'legal_entity_name_third')) {
        $normalized['par_data_legal_entity'][] = ParDataLegalEntity::create();
      }
    }

    // Set the coordinated member values.
    $normalized['par_data_coordinated_business']->set('date_membership_began', $this->getValue($data, 'membership_start'));
    if ($this->getValue($data, 'ceased') && $normalized['par_data_coordinated_business'] instanceof ParDataCoordinatedBusiness) {
      $normalized['par_data_coordinated_business']->cease($this->getValue($data, 'membership_end'), FALSE);
    }

    // Set the organisation details.
    $normalized['par_data_organisation']->set('organisation_name', $this->getValue($data, 'organisation_name'));

    // Set the person details.
    $normalized['par_data_person']->set('first_name', $this->getValue($data, 'first_name'));
    $normalized['par_data_person']->set('last_name', $this->getValue($data, 'last_name'));
    $normalized['par_data_person']->set('work_phone', $this->getValue($data, 'work_phone'));
    $normalized['par_data_person']->set('mobile_phone', $this->getValue($data, 'mobile_phone'));
    $normalized['par_data_person']->set('email', $this->getValue($data, 'email'));

    // Set the address details.
    $address = [
      'country_code' => $this->getValue($data, 'country_code'),
      'address_line1' => $this->getValue($data, 'address_line_1'),
      'address_line2' => $this->getValue($data, 'address_line_2'),
      'locality' => $this->getValue($data, 'town'),
      'administrative_area' => $this->getValue($data, 'county'),
      'postal_code' => $this->getValue($data, 'postcode'),
    ];
    $normalized['par_data_premises']->set('address', $address);
    $normalized['par_data_premises']->set('nation', $this->getValue($data, 'nation'));

    // Set the legal entities.
    $normalized['par_data_legal_entity'][0]->set('registered_name', $this->getValue($data, 'legal_entity_name_first'));
    $normalized['par_data_legal_entity'][0]->set('registered_number', $this->getValue($data, 'legal_entity_number_first'));
    $normalized['par_data_legal_entity'][0]->set('legal_entity_type', $this->getValue($data, 'legal_entity_type_first'));
    // Set the second legal entity.
    if ($this->getValue($data, 'legal_entity_name_second')) {
      if (!isset($normalized['par_data_legal_entity'][1])) {
        $normalized['par_data_legal_entity'][1] = ParDataLegalEntity::create();
      }
      $normalized['par_data_legal_entity'][1]->set('registered_name', $this->getValue($data, 'legal_entity_name_second'));
      $normalized['par_data_legal_entity'][1]->set('registered_number', $this->getValue($data, 'legal_entity_number_second'));
      $normalized['par_data_legal_entity'][1]->set('legal_entity_type', $this->getValue($data, 'legal_entity_type_second'));
    }
    // Set the third legal entity.
    if ($this->getValue($data, 'legal_entity_name_third')) {
      if (!isset($normalized['par_data_legal_entity'][21])) {
        $normalized['par_data_legal_entity'][2] = ParDataLegalEntity::create();
      }
      $normalized['par_data_legal_entity'][2]->set('registered_name', $this->getValue($data, 'legal_entity_name_third'));
      $normalized['par_data_legal_entity'][2]->set('registered_number', $this->getValue($data, 'legal_entity_number_third'));
      $normalized['par_data_legal_entity'][2]->set('legal_entity_type', $this->getValue($data, 'legal_entity_type_third'));
    }

    return $normalized;
  }

  /**
   * De-normalize this member into an array of data.
   *
   * @param ParDataCoordinatedBusiness $member
   *
   * @return array
   */
  protected function denormalize(ParDataCoordinatedBusiness $member) {
    $entities = $this->extract($member);

    // We need to get the bundle information to transform bundles into labels.
    /** @var ParDataCoordinatedBusinessType $par_data_coordinated_business_type */
    /** @var ParDataOrganisationType $par_data_organisation_type */
    /** @var ParDataPremisesType $par_data_premises_type */
    /** @var ParDataPersonType $par_data_person_type */
    /** @var ParDataLegalEntityType $par_data_legal_entity_type */
    foreach ($entities as $entity_type => $entity) {
      ${$entity_type . "_type"} = $this->getParDataManager()->getParBundleEntity($entity_type);
    }

    /** @var ParDataCoordinatedBusiness $par_data_coordinated_business */
    /** @var ParDataOrganisation $par_data_organisation */
    /** @var ParDataPremises $par_data_premises */
    /** @var ParDataPerson $par_data_person */
    /** @var ParDataLegalEntity[] $par_data_legal_entity */
    extract($entities);

    // There are two possible values for nation depending
    // on whether it is in the UK or outside.
    $nation = $par_data_premises ? $par_data_premises_type->getAllowedFieldlabel('nation', $par_data_premises->get('nation')->getString()) : NULL;
    try {
      $nation = !$nation && $par_data_premises ? $this->getCountryRepository()->get($par_data_premises->get('address')->first()->get('country_code')->getString()) : '';
    }
    catch (UnknownCountryException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
    }

    return [
      $this->getMapping('organisation_name') => $par_data_organisation ? $par_data_organisation->get('organisation_name')->getString() : '',
      $this->getMapping('address_line_1') => $par_data_premises ? $par_data_premises->get('address')->first()->get('address_line1')->getString() : '',
      $this->getMapping('address_line_2') => $par_data_premises ? $par_data_premises->get('address')->first()->get('address_line2')->getString() : '',
      $this->getMapping('town') => $par_data_premises ? $par_data_premises->get('address')->first()->get('locality')->getString() : '',
      $this->getMapping('county') => $par_data_premises ? $par_data_premises->get('address')->first()->get('administrative_area')->getString() : '',
      $this->getMapping('postcode') => $par_data_premises ? $par_data_premises->get('address')->first()->get('postal_code')->getString() : '',
      $this->getMapping('nation') => $nation,
      $this->getMapping('first_name') => $par_data_person ? $par_data_person->get('first_name')->getString() : '',
      $this->getMapping('last_name') => $par_data_person ? $par_data_person->get('last_name')->getString() : '',
      $this->getMapping('work_phone') => $par_data_person ? $par_data_person->get('work_phone')->getString() : '',
      $this->getMapping('mobile_phone') => $par_data_person ? $par_data_person->get('mobile_phone')->getString() : '',
      $this->getMapping('email') => $par_data_person ? $par_data_person->get('email')->getString() : '',
      $this->getMapping('membership_start') => $par_data_coordinated_business ? $par_data_coordinated_business->get('date_membership_began')->getString() : '',
      $this->getMapping('membership_end') => $par_data_coordinated_business ? $par_data_coordinated_business->get('date_membership_ceased')->getString() : '',
      $this->getMapping('covered') => $par_data_coordinated_business_type->getBooleanFieldLabel($par_data_coordinated_business->getBoolean('covered_by_inspection')),
      $this->getMapping('legal_entity_name_first') => isset($par_data_legal_entity[0]) ? $par_data_legal_entity[0]->get('registered_name')->getString() : '',
      $this->getMapping('legal_entity_type_first') => isset($par_data_legal_entity[0]) ?
        $par_data_premises_type->getAllowedFieldlabel('legal_entity_type', $par_data_legal_entity[0]->get('legal_entity_type')->getString()) : '',
      $this->getMapping('legal_entity_number_first') => isset($par_data_legal_entity[0]) ? $par_data_legal_entity[0]->get('registered_number')->getString() : '',
      $this->getMapping('legal_entity_name_second') => isset($par_data_legal_entity[1]) ? $par_data_legal_entity[1]->get('registered_name')->getString() : '',
      $this->getMapping('legal_entity_type_second') => isset($par_data_legal_entity[1]) ?
        $par_data_premises_type->getAllowedFieldlabel('legal_entity_type', $par_data_legal_entity[1]->get('legal_entity_type')->getString()) : '',
      $this->getMapping('legal_entity_type_second') => isset($par_data_legal_entity[1]) ? $par_data_legal_entity[1]->get('registered_number')->getString() : '',
      $this->getMapping('legal_entity_name_third') => isset($par_data_legal_entity[2]) ? $par_data_legal_entity[2]->get('registered_name')->getString() : '',
      $this->getMapping('legal_entity_type_third') => isset($par_data_legal_entity[2]) ?
        $par_data_premises_type->getAllowedFieldlabel('legal_entity_type', $par_data_legal_entity[2]->get('legal_entity_type')->getString()) : '',
      $this->getMapping('legal_entity_type_third') => isset($par_data_legal_entity[2]) ? $par_data_legal_entity[2]->get('registered_number')->getString() : '',
    ];
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
      $data = $this->sanitize($this->getSerializer()->decode($csv, 'csv'));

      // We have a limit of that we can process to, this limit
      // is tested with and anything over cannot be supported.
      if (count($data) < 2 || count($data) > self::MAX_ROW_LIMIT) {
        throw new ParDataException('There are too many or too few rows in this CSV file.');
      }

      // It is important to add the partnership ID to each row for later processing.
      foreach ($data as $column => $row) {
        $partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
        if ($partnership) {
          $data[$column]['Partnership id'] = $this->getFlowDataHandler()->getParameter('par_data_partnership')->id();
        }
        else {
          throw new ParDataException('The partnership can\'t be identified.');
        }
      }
    }
    catch (UnexpectedValueException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
      return $exception->getMessage();
    }
    catch (ParDataException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
      return $exception->getMessage();
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
    $name = str_replace(' ', '_', "Member list for " . lcfirst($par_data_partnership->label()));
    $file = file_save_data($data, $directory . $name . '.' . self::FILE_EXTENSION, FILE_EXISTS_REPLACE);

    // Set the reference fields, useful for keeping track of
    // which authorities and organisations the csv belongs to.
    $file->set('field_authority', $par_data_partnership->getAuthority(TRUE));
    $file->set('field_organisation', $par_data_partnership->getOrganisation(TRUE));
    $file->save();
    
    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {
    return array_values($this->getMappings());
  }

  /**
   * {@inheritdoc}
   */
  public function lock(ParDataPartnership $par_data_partnership) {
    if (!$par_data_partnership->lockMembership()) {
      throw new ParCsvProcessingException('The membership list could not be locked, processing cannot continue.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function unlock(ParDataPartnership $par_data_partnership) {
    $par_data_partnership->unlockMembership();
  }

  /**
   * Santizie all csv rows.
   */
  protected function sanitize(array $rows) {
    $data = [];

    foreach ($rows as $index => $row) {
      $data[$index] = [];

      foreach ($row as $column => $value) {
        $data[$index][$column] = Xss::filter(trim($value));
      }
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $rows) {
    $errors = [];

    foreach ($rows as $index => $row) {
      // Check that all headings are supported.
      $diff_keys = array_diff_key($row, $this->getColumns());
      if (!empty($diff_Keys)) {
        $errors[] = new ParCsvViolation($index+1, NULL, 'The column headings are incorrect or missing.');
      }

      $validator = Validation::createValidator();
      foreach ($this->getConstraints() as $key => $constraints) {
        $column = $this->getMapping($key);
        // Ensure case insensitive validation.
        $value = strtolower($row[$column]);
        if (NULL !== $constraints) {
          $violations = $validator->validate($value, $constraints);

          foreach ($violations as $violation) {
            $errors[] = new ParCsvViolation($index+1, $column, $violation->getMessage());
          }
        }
      }
    }

    return !empty($errors) ? $errors : NULL;
  }

  public function backup(ParDataPartnership $par_data_partnership) {
    $existing_members = $par_data_partnership->getCoordinatedMember();

    return $existing_members;
  }

  /**
   * {@inheritdoc}
   *
   * The headings are defined once here (for compilation)
   * and once on the self::getMappings() method for validation.
   */
  public function generate(array $members) {
    $rows = [];

    foreach ($members as $index => $par_data_coordinated_business) {
      $rows[$index] = $this->denormalize($par_data_coordinated_business);
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
   * @param $data
   * @param ParDataPartnership $par_data_partnership
   *
   *
   * @return array
   *   An array of members that were processed and saved.
   */
  public function process($data, ParDataPartnership $par_data_partnership) {
    $new_members = [];

    foreach ($data as $index => $row) {
      $member = $this->normalize($row);

      if ($member) {
        /** @var ParDataCoordinatedBusiness $par_data_coordinated_business */
        /** @var ParDataOrganisation $par_data_organisation */
        /** @var ParDataPremises $par_data_premises */
        /** @var ParDataPerson $par_data_person */
        /** @var ParDataLegalEntity[] $par_data_legal_entity */
        extract($member);

        // Try to save the address.
        try {
          $par_data_premises->save();
        }
        catch (EntityStorageException $exception) {
            $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
        }

        // Try to save the person.
        try {
          $par_data_person->save();
        }
        catch (EntityStorageException $exception) {
          $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
        }

        foreach ($par_data_legal_entity as $e) {
          // Try to save the legal entity.
          try {
            $e->save();
          }
          catch (EntityStorageException $exception) {
            $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
          }
        }

        // Add the references to the organisation.
        if ($par_data_premises->id()) {
          $par_data_organisation->set('field_premises', $par_data_premises);
        }
        if ($par_data_person->id()) {
          $par_data_organisation->set('field_person', $par_data_person);
        }
        if (current($par_data_legal_entity)->id()) {
          $par_data_organisation->set('field_legal_entity', $par_data_legal_entity);
        }

        // Try to save the organisation.
        try {
          $saved = $par_data_organisation->save();
        }
        catch (EntityStorageException $exception) {
          $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
        }

        if (isset($saved) && !empty($saved)) {
          $par_data_coordinated_business->set('field_organisation', $par_data_organisation);

          // Try to save the coordinated member.
          try {
            if ($par_data_coordinated_business->save()) {
              $new_members[$par_data_coordinated_business->id()] = $par_data_coordinated_business;
            }
          }
          catch (EntityStorageException $exception) {
            $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
          }
        }
      }
    }

    return $new_members;
  }

  public function update(ParDataPartnership $par_data_partnership, $members) {
    $new_members = array_filter($members, function($v) {
      return ($v instanceof ParDataCoordinatedBusiness);
    });

    $par_data_partnership->set('field_coordinated_business', array_values($new_members));
    return $par_data_partnership->save();
  }

  /**
   * Whether the member can be deleted, only appropriate for CSV upload.
   *
   * @param ParDataCoordinatedBusiness $member
   *
   * @return bool
   *   Whether the member can be deleted.
   */
  public function canDestroyMember(ParDataCoordinatedBusiness $member) {
    if ($par_data_organisation = $member->getOrganisation(TRUE)) {
      $par_data_enforcement_notices = $par_data_organisation->getRelationships('par_data_enforcement_notice');
      foreach ($par_data_enforcement_notices as $entity) {
        if ($entity->isLiving()) {
          return FALSE;
        }
      }
    }

    return TRUE;
  }

  public function clean($old, $new, $par_data_partnership) {
    $diff = array_udiff($old, $new, function ($a, $b) {
        return $a->id() - $b->id();
      }
    );

    $maintain = [];

    foreach ($diff as $member) {
      // So long as the member isn't required by another entity then we can permanently remove it.
      if (!$this->canDestroyMember($member)) {
        $date = $this->getDateFormatter()->format(time(), 'custom', 'Y-m-d');
        $member->cease($date, TRUE);

        // Make sure the member is added back to the partnership.
        $par_data_partnership->get('field_coordinated_business')->appendItem($member->id());
      }
      else {
        $member->destroy();

        // Remove all referenced entities also.
        // @TODO Make sure these entities are not removed if they are referenced by something else.
        foreach ($member->getDependents() as $entity) {
          $entity->destroy();
        }
      }
    }

    // Save the partnership with any members that were carried across.
    $par_data_partnership->save();
  }

  /**
   * AJAX helper form generating the downloaded member file.
   *
   * @return mixed
   */
  public static function _ajaxDownload(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $par_data_partnership = ParDataPartnership::load($form_state->getValue('par_data_partnership'));

    // Generate and save member list.
    $file = $csv_handler->download($par_data_partnership);

    // Redirect to saved file.
    if ($file) {
      $url = $file->downloadUrl()->toString();
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
    \Drupal::service('messenger')->deleteAll();

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
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $csv_handler_class = (new \ReflectionClass($csv_handler))->getName();

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
    $chunks = !empty($members) ? array_chunk($members, self::BATCH_LIMIT) : [];
    foreach ($chunks as $chunk) {
      $batch['operations'][] = [
        [$csv_handler_class, 'bactch_generate'],
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

  public function upload($data, $par_data_partnership) {
    try {
      // 1. Lock the member list.
      $locked = $this->lock($par_data_partnership);

      // 2. Backup the existing list.
      $old_members = $this->backup($par_data_partnership);

      // 3. Process the new members self::process().
      $members = $this->process($data, $par_data_partnership);

      // 4. Replace old members with new members.
      $updated = $this->update($par_data_partnership, $members);

      // 5. Remove old members that are not in the current list.
      $this->clean($old_members, $members, $par_data_partnership);

      // 6. Unlock the member list.
      $this->unlock($par_data_partnership);

      return (bool) $updated;
    }
    catch (ParCsvProcessingException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
      return FALSE;
    }
  }

  public function batchUpload($data, $par_data_partnership) {
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $csv_handler_class = (new \ReflectionClass($csv_handler))->getName();

    // Configure the batch.
    $batch = [
      'title' => t('Process Members List CSV'),
      'operations' => [],
      'library' => ['par_member_upload_flows/par-batch'],
      'init_message' => t('Processing the new member list'),
      'progress_message' => t('Processing...  (do not leave this page)'),
      'error_message' => t('There has been an error processing the member list, if this issue persists please contact the helpdesk.'),
    ];

    // 1. Lock the member list.
    try {
      $locked = $this->lock($par_data_partnership);
    }
    catch (ParCsvProcessingException $exception) {
      $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning($exception);
      return FALSE;
    }

    // 2. Backup the existing list.
    $old_members = $this->backup($par_data_partnership);

    // 3. Process the new members self::process().
    $chunks = !empty($data) ? array_chunk($data, 100) : [];
    foreach ($chunks as $data) {
      $batch['operations'][] = [
        [$csv_handler_class, 'batch__process'],
        [$data, $par_data_partnership]
      ];
    }

    // 4. Replace old members with new members.
    $batch['operations'][] = [
      [$csv_handler_class, 'batch__update'],
      [$par_data_partnership]
    ];

    // 5. Remove old members that are not in the current list.
    $batch['operations'][] = [
      [$csv_handler_class, 'batch__clean'],
      [$old_members, $par_data_partnership]
    ];

    // 6. Unlock the member list.
    $batch['operations'][] = [
      [$csv_handler_class, 'batch__unlock'],
      [$par_data_partnership]
    ];

    batch_set($batch);

    return TRUE;
  }

  public static function batch__process($data, ParDataPartnership $par_data_partnership, &$context) {
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $context['message'] = 'Processing new members.';

    $members = $csv_handler->process($data, $par_data_partnership);

    if ($members) {
      $context['results'] = $members;
    }
  }

  public static function batch__update(ParDataPartnership $par_data_partnership, &$context) {
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $context['message'] = 'Updating the member list.';

    $members = $context['results'];
    $updated = $csv_handler->update($par_data_partnership, $members);

    if ($updated) {
      $context['sandbox']['updated'] = TRUE;
    }
  }

  public static function batch__clean($old_members, $par_data_partnership, &$context) {
    $csv_handler = \Drupal::service('par_member_upload_flows.csv_handler');
    $context['message'] = 'Cleaning up old members.';

    $new_members = $context['results'];

    $csv_handler->clean($old_members, $new_members, $par_data_partnership);
  }

}
