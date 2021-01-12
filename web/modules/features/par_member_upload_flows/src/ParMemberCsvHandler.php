<?php

namespace Drupal\par_member_upload_flows;

use CommerceGuys\Addressing\Exception\UnknownCountryException;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\File\FileSystemInterface;
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
use Drupal\par_validation\Plugin\Validation\Constraint\FutureDate;
use Drupal\par_validation\Plugin\Validation\Constraint\PastDate;
use Drupal\par_validation\Plugin\Validation\Constraint\PastDateValidator;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
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
   * Set the default date formats.
   */
  const DATE_FORMAT = 'd/m/Y';
  const DATETIME_FORMAT = 'Y-m-d';

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
    $mappings = [
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
      'legal_entity_number_first' => 'Legal Entity Number (first)',
      'legal_entity_type_first' => 'Legal Entity Type (first)',
      'legal_entity_name_second' => 'Legal Entity Name (second)',
      'legal_entity_number_second' => 'Legal Entity Number (second)',
      'legal_entity_type_second' => 'Legal Entity Type (second)',
      'legal_entity_name_third' => 'Legal Entity Name (third)',
      'legal_entity_number_third' => 'Legal Entity Number (third)',
      'legal_entity_type_third' => 'Legal Entity Type (third)',
    ];

    return array_map('strtolower', $mappings);
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

  /**
   * A helper function to convert a string date representation to a valid DateTime object.
   *
   * @param $string
   * @param string $format
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime|null
   */
  public function stringToDate($string, $format = self::DATETIME_FORMAT) {
    if (isset($string) && !empty($string)) {
      try {
        $date = DrupalDateTime::createFromFormat($format . " H:i:s", $string . " 23:59:59", NULL);
        if (empty($date)) {
          throw new \Exception('The date entered cannot be converted to a valid date.');
        }

        return $date;
      }
      catch (\Exception $e) {
        $this->getLogger(self::PAR_LOGGER_CHANNEL)->warning('The date entered cannot be converted to a valid date.');
      }
    }

    return NULL;
  }

  protected function getConstraints($row) {
    /** @var ParDataPremisesType $par_data_premises_type */
    $par_data_premises_type = $this->getParDataManager()->getParBundleEntity('par_data_premises');
    $par_data_legal_entity_type = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    // The end date must be greater than the start date @see PAR-1477, however,
    // the start date must first be converted to a valid DateTime string.
    $start_date_input = isset($row[$this->getMapping('membership_start')]) ? $row[$this->getMapping('membership_start')] : '';
    if ($start_date_input) {
      try {
        $date = \DateTime::createFromFormat(self::DATE_FORMAT . " H:i:s", $start_date_input . " 23:59:59");
        if (empty($date) || !$date instanceof \DateTimeInterface) {
          throw new \Exception('The start date cannot be used to compare the end date.');
        }
        $start_date = $date->format(\DateTimeInterface::ATOM);
      }
      catch (\Exception $e) {
        $start_date = NULL;
      }
    }

    $legal_entity_options = $par_data_legal_entity_type->getAllowedValues('legal_entity_type');
    $country_options = $this->getCountryRepository()->getList(NULL) + $par_data_premises_type->getAllowedValues('nation');
    $boolean_options = ['Yes', 'No', 'Y', 'N'];
    return [
      'organisation_name' => [
        new Length(['max' => 500]),
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
      ],
      'email' => [
        new Length(['max' => 500]),
        new Email(),
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
      ],
      'membership_start' => [
        new DateTime(['format' => self::DATE_FORMAT]),
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
        new PastDate(['value' => 'tomorrow']),
      ],
      'membership_end' => [
        new DateTime(['format' => self::DATE_FORMAT]),
        new FutureDate([
          'value' => isset($start_date) ? $start_date : 'today',
          'message' => 'The membership end date should be after the start date.',
        ]),
      ],
      'legal_entity_name_first' => [
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
      ],
      'address_line_1' => [
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
      ],
      'nation' => [
        new NotBlank(),
        new Choice([
          'choices' => array_map('strtolower', $country_options),
          'message' => 'The value you entered is not a valid selection, please see the Member Guidance Page for a full list of available country codes.',
        ]),
      ],
      'covered' => [
        new Choice([
          'choices' => array_map('strtolower', $boolean_options),
          'message' => 'The value you entered is not a valid selection, please choose `Yes` or `No`.',
        ]),
      ],
      'legal_entity_type_first' => [
        new NotBlank([
          'message' => 'The value could not be found.',
        ]),
        new Choice([
          'choices' => array_map('strtolower', $legal_entity_options),
          'message' => 'The value you entered is not a valid selection, please see the Member Guidance Page for a full list of legal entity types.',
        ]),
      ],
      'legal_entity_type_second' => [
        new Choice([
          'choices' => array_map('strtolower', $legal_entity_options),
          'message' => 'The value you entered is not a valid selection, please see the Member Guidance Page for a full list of legal entity types.',
        ]),
      ],
      'legal_entity_type_third' => [
        new Choice([
          'choices' => array_map('strtolower', $legal_entity_options),
          'message' => 'The value you entered is not a valid selection, please see the Member Guidance Page for a full list of legal entity types.',
        ]),
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
              $date = DrupalDateTime::createFromFormat(self::DATE_FORMAT, $value, NULL, ['validate_format' => FALSE]);
              $data[$column] = $date->format(self::DATETIME_FORMAT);
            }
            catch (\Exception $e) {

            }

            break;

          case 'membership_end':
            try {
              // Create the date, setting the time to the last possible time in the day.
              $cease_date = DrupalDateTime::createFromFormat(self::DATE_FORMAT . " H:i:s", $value . " 23:59:59", NULL, ['validate_format' => FALSE]);
              $data[$column] = $cease_date->format(self::DATETIME_FORMAT);
            }
            catch (\Exception $e) {

            }

            $current_date = new DrupalDateTime();

            // Only cease the membership if the expiry date is in the past.
            if ($cease_date < $current_date) {
              $data[$this->getMapping('ceased')] = TRUE;
            }

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

          // Only use this member if the first legal entity name is the same
          // or if there is no legal entity.
          $organisation = $member->getOrganisation(TRUE);
          $legal_entity = $organisation ? $organisation->getLegalEntity(TRUE) : NULL;

          if ($legal_entity
              && $this->getValue($data, 'legal_entity_name_first')
              && $legal_entity->get('registered_name')->getString() !== $this->getValue($data, 'legal_entity_name_first')) {
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
    $normalized['par_data_coordinated_business']->set('covered_by_inspection', $this->getValue($data, 'covered'));

    $start_date = $this->stringToDate($this->getValue($data, 'membership_start'));
    $end_date = $this->stringToDate($this->getValue($data, 'membership_end'));
    if ($start_date) {
      $normalized['par_data_coordinated_business']->set('date_membership_began', $start_date->format(self::DATETIME_FORMAT));
    }
    $ceased = $this->getValue($data, 'ceased');
    if ($end_date && $ceased) {
      $normalized['par_data_coordinated_business']->cease($end_date, FALSE);
    }
    else {
      $normalized['par_data_coordinated_business']->reinstate($end_date, FALSE);
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
    $normalized['par_data_premises']->setNation($this->getValue($data, 'nation'));

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

    $address = $par_data_premises ? $par_data_premises->get('address')->first() : NULL;

    return [
      $this->getMapping('organisation_name') => $par_data_organisation ? $par_data_organisation->get('organisation_name')->getString() : '',
      $this->getMapping('address_line_1') => $address ? $address->get('address_line1')->getString() : '',
      $this->getMapping('address_line_2') => $address ? $address->get('address_line2')->getString() : '',
      $this->getMapping('town') => $address ? $address->get('locality')->getString() : '',
      $this->getMapping('county') => $address ? $address->get('administrative_area')->getString() : '',
      $this->getMapping('postcode') => $address ? $address->get('postal_code')->getString() : '',
      $this->getMapping('nation') => $address ? $par_data_premises->getCountry() : '',
      $this->getMapping('first_name') => $par_data_person ? $par_data_person->getFirstName() : '',
      $this->getMapping('last_name') => $par_data_person ? $par_data_person->getLastName() : '',
      $this->getMapping('work_phone') => $par_data_person ? $par_data_person->get('work_phone')->getString() : '',
      $this->getMapping('mobile_phone') => $par_data_person ? $par_data_person->get('mobile_phone')->getString() : '',
      $this->getMapping('email') => $par_data_person ? $par_data_person->getEmail() : '',
      $this->getMapping('membership_start') => $par_data_coordinated_business ? $par_data_coordinated_business->getStartDate() : '',
      $this->getMapping('membership_end') => $par_data_coordinated_business ? $par_data_coordinated_business->getEndDate() : '',
      $this->getMapping('covered') => $par_data_coordinated_business ? $par_data_coordinated_business->getCovered() : '',
      $this->getMapping('legal_entity_name_first') => isset($par_data_legal_entity[0]) ? $par_data_legal_entity[0]->getName() : '',
      $this->getMapping('legal_entity_type_first') => isset($par_data_legal_entity[0]) ? $par_data_legal_entity[0]->getType() : '',
      $this->getMapping('legal_entity_number_first') => isset($par_data_legal_entity[0]) ? $par_data_legal_entity[0]->getRegisteredNumber() : '',
      $this->getMapping('legal_entity_name_second') => isset($par_data_legal_entity[1]) ? $par_data_legal_entity[1]->getName() : '',
      $this->getMapping('legal_entity_type_second') => isset($par_data_legal_entity[1]) ? $par_data_legal_entity[1]->getType() : '',
      $this->getMapping('legal_entity_number_second') => isset($par_data_legal_entity[1]) ? $par_data_legal_entity[1]->getRegisteredNumber() : '',
      $this->getMapping('legal_entity_name_third') => isset($par_data_legal_entity[2]) ? $par_data_legal_entity[2]->getName() : '',
      $this->getMapping('legal_entity_type_third') => isset($par_data_legal_entity[2]) ? $par_data_legal_entity[2]->getType() : '',
      $this->getMapping('legal_entity_number_third') => isset($par_data_legal_entity[2]) ? $par_data_legal_entity[2]->getRegisteredNumber() : '',
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
          $data[$column]['partnership id'] = $this->getFlowDataHandler()->getParameter('par_data_partnership')->id();
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
    $file = file_save_data($data, $directory . $name . '.' . self::FILE_EXTENSION, FileSystemInterface::EXISTS_REPLACE);

    // Set the reference fields, useful for keeping track of
    // which authorities and organisations the csv belongs to.
    if ($file) {
      $file->set('field_authority', $par_data_partnership->getAuthority(TRUE));
      $file->set('field_organisation', $par_data_partnership->getOrganisation(TRUE));
      $file->save();
    }

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

      // Clear all empty values.
      $row = NestedArray::filter($row);

      // Ignore empty rows.
      if (!empty($row)) {
        foreach ($row as $column => $value) {
          $data[$index][strtolower($column)] = Xss::filter(trim($value));
        }
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
        $errors[] = new ParCsvViolation($index+2, NULL, 'The column headings are incorrect or missing.');
      }

      $validator = Validation::createValidator();
      foreach ($this->getConstraints($row) as $key => $constraints) {
        $column = $this->getMapping($key);

        // Ensure case insensitive validation.
        $value = isset($row[$column]) ? $row[$column] : NULL;

        if (NULL !== $constraints) {
          // Ensure all strings are validated as case insensitive values.
          $value = is_string($value) ? strtolower($value) : $value;

          // Validate constraints.
          $violations = $validator->validate($value, $constraints);

          foreach ($violations as $violation) {
            $errors[] = new ParCsvViolation($index+2, $column, $violation->getMessage());
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
      foreach ($par_data_enforcement_notices as $uuid => $relationship) {
        return FALSE;
      }
    }

    return TRUE;
  }

  public function clean($old, $new, $par_data_partnership) {
    $diff = array_udiff($old, $new, function ($a, $b) {
        return $a->id() - $b->id();
      }
    );

    // Re-load the partnership to get any updates perforced during batch processes.
    $par_data_partnership = ParDataPartnership::load($par_data_partnership->id());

    foreach ($diff as $member) {
      // As per PAR-1438 do not remove members permanently because this changes
      // the nature of an active partnership and can cause other data to be
      // incorrectly removed (@see PAR-1439).
      $current_date = new DrupalDateTime();
      $member->cease($current_date);
      // Make sure the member is added back to the partnership.
      $par_data_partnership->get('field_coordinated_business')->appendItem($member->id());
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
    $chunks = !empty($data) ? array_chunk($data, self::BATCH_LIMIT) : [];
    foreach ($chunks as $d) {
      $batch['operations'][] = [
        [$csv_handler_class, 'batch__process'],
        [$d, $par_data_partnership]
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
      $context['results'] = array_merge($context['results'], $members);
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
