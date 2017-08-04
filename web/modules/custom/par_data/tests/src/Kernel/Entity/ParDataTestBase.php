<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataAdviceType;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementActionType;
use Drupal\par_data\Entity\ParDataEnforcementNoticeType;
use Drupal\par_data\Entity\ParDataInspectionPlan;
use Drupal\par_data\Entity\ParDataInspectionPlanType;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataLegalEntityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;
use Drupal\par_data\Entity\ParDataPartnershipType;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\par_data\Entity\ParDataPremises;
use Drupal\par_data\Entity\ParDataPremisesType;
use Drupal\par_data\Entity\ParDataRegulatoryFunction;
use Drupal\par_data\Entity\ParDataRegulatoryFunctionType;
use Drupal\par_data\Entity\ParDataSicCode;
use Drupal\par_data\Entity\ParDataSicCodeType;

/**
 * Tests PAR Data test base.
 *
 * @group PAR Data
 */
class ParDataTestBase extends EntityKernelTestBase {

  static $modules = ['trance', 'par_data', 'par_data_config', 'address', 'datetime', 'datetime_range', 'file', 'file_entity'];

  protected $account = 1;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    parent::setUp();

    $this->account = $this->createUser();

    // Mimic some of the functionality in \Drupal\Tests\file\Kernel\FileManagedUnitTestBase
    $this->setUpFilesystem();

    // Install out entity hooks.
    $entity_types = [
      'par_data_advice',
      'par_data_authority',
      'par_data_enforcement_action',
      'par_data_enforcement_notice',
      'par_data_inspection_plan',
      'par_data_legal_entity',
      'par_data_organisation',
      'par_data_partnership',
      'par_data_person',
      'par_data_premises',
      'par_data_regulatory_function',
      'par_data_sic_code',
    ];

    foreach ($entity_types as $type) {
      // Set up schema for par_data.
      $this->installEntitySchema($type);
    }

    // Install config for par_data if required.
    $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataAdviceType::create([
      'id' => 'advice',
      'label' => 'Advice',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataAuthorityType::create([
      'id' => 'authority',
      'label' => 'Authority',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataEnforcementActionType::create([
      'id' => 'enforcement_action',
      'label' => 'Enforcement Action',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataEnforcementNoticeType::create([
      'id' => 'enforcement_notice',
      'label' => 'Enforcement Notice',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataInspectionPlanType::create([
      'id' => 'inspection_plan',
      'label' => 'Inspection Plan',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataLegalEntityType::create([
      'id' => 'legal_entity',
      'label' => 'Legal Entity',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataOrganisationType::create([
      'id' => 'coordinator',
      'label' => 'Coordinator Organisation',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataOrganisationType::create([
      'id' => 'business',
      'label' => 'Business Organisation',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataPartnershipType::create([
      'id' => 'partnership',
      'label' => 'Partnership',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataPersonType::create([
      'id' => 'person',
      'label' => 'Person',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataPremisesType::create([
      'id' => 'premises',
      'label' => 'Premises',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataRegulatoryFunctionType::create([
      'id' => 'regulatory_function',
      'label' => 'Regulatory Function',
    ]);
    $type->save();

    // Create the entity bundles required for testing.
    $type = ParDataSicCodeType::create([
      'id' => 'sic_code',
      'label' => 'SIC Code',
    ]);
    $type->save();

    // Install the feature config
    $this->installConfig('par_data_config');
  }

  public function getBaseValues() {
    return [
      'name' => 'test',
      'uid' => $this->account,
      'type' => 'UNKNOWN',
    ];
  }

  public function getAdviceValues() {
    // We need to create a Regulatory Function first.
    $regulatory_function = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $regulatory_function->save();

    return [
      'type' => 'advice',
      'advice_type' => 'To Local Authority',
      'notes' => $this->randomString(1000),
      'visible_authority' => TRUE,
      'visible_coordinator' => TRUE,
      'visible_business' => TRUE,
      'document' => [
        ''
      ],
      'regulatory_function' => [
        $regulatory_function->id(),
      ],
    ] + $this->getBaseValues();
  }

  public function getAuthorityValues() {
    // We need to create a Person first.
    $person = ParDataPerson::create($this->getPersonValues());
    $person->save();

    // We need to create a Regulatory Function first.
    $regulatory_function = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $regulatory_function->save();

    // We need to create an Organisation first.
    $premises = ParDataPremises::create($this->getPremisesValues());
    $premises->save();

    return [
      'type' => 'authority',
      'authority_name' => 'Test Authority',
      'authority_type' => 'Local Authority',
      'nation' => 'Wales',
      'ons_code' => '123456',
      'comments' => $this->randomString(1000),
      'person' => [
        $person->id(),
      ],
      'regulatory_function' => [
        $regulatory_function->id(),
      ],
      'allowed_regulatory_function' => [
        $regulatory_function->id(),
      ],
      'premises' => [
        $premises->id(),
      ]
    ] + $this->getBaseValues();
  }

  public function getEnforcementActionValues() {
    // We need to create an Advice first.
    $advice = ParDataAdvice::create($this->getAdviceValues());
    $advice->save();

    // We need to create a Regulatory Function first.
    $regulatory_function = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $regulatory_function->save();

    return [
        'type' => 'enforcement_action',
        'title' => 'Test Enforcement Action',
        'details' => $this->randomString(1000),
        'enforcement_action_status' => 'Enforced',
        'enforcement_action_notes' => $this->randomString(1000),
        'primary_authority_status' => 'Enforced',
        'primary_authority_notes' => $this->randomString(1000),
        'referral_notes' => $this->randomString(1000),
        'blocked_advice' => [
          $advice->id(),
        ],
        'regulatory_function' => [
          $regulatory_function->id(),
        ],
      ] + $this->getBaseValues();

  }

  public function getEnforcementNoticeValues() {
    // We need to create a Primary Authority first.
    $primary_authority = ParDataAuthority::create($this->getAuthorityValues());
    $primary_authority->save();

    // We need to create an Enforcing Authority first.
    $enforcing_authority = ParDataAuthority::create($this->getAuthorityValues());
    $enforcing_authority->save();

    // We need to create a Legal Entity first.
    $legal_entity = ParDataLegalEntity::create($this->getLegalEntityValues());
    $legal_entity->save();

    // We need to create an Enforcement Action first.
    $enforcement_action = ParDataEnforcementAction::create($this->getEnforcementActionValues());
    $enforcement_action->save();

    return [
      'type' => 'enforcement_notice',
      'notice_type' => 'Closure',
      'notice_date' => '2017-10-01',
      'summary' => $this->randomString(1000),
      'primary_authority' => [
        $primary_authority->id(),
      ],
      'enforcing_authority' => [
        $enforcing_authority->id(),
      ],
      'legal_entity' => [
        $legal_entity->id(),
      ],
      'enforcement_action' => [
        $enforcement_action->id(),
      ],
    ] + $this->getBaseValues();

  }

  public function getInspectionPlanValues() {
    return [
      'type' => 'inspection_plan',
      'valid_date' => [
        'value' => '2016-01-01',
        'end_value' => '2018-01-01',
      ],
      'approved_rd_executive' => TRUE,
      'consulted_national_regulator' => TRUE,
      'inspection_status' => 'Active',
      'document' => [
        ''
      ],
    ] + $this->getBaseValues();
  }

  public function getLegalEntityValues() {
    return [
      'type' => 'legal_entity',
      'registered_name' => 'Jo\' Coffee Ltd',
      'registered_number' => '0123456789',
      'legal_entity_type' => 'Limited Company',
    ] + $this->getBaseValues();
  }

  public function getOrganisationCoordinatorValues() {
    // We need to create an SIC Code first.
    $sic_code = ParDataSicCode::create($this->getSicCodeValues());
    $sic_code->save();

    // We need to create a Person first.
    $person = ParDataPerson::create($this->getPersonValues());
    $person->save();

    // We need to create a Premises first.
    $premises = ParDataPremises::create($this->getPremisesValues());
    $premises->save();

    // We need to create a Legal Entity first.
    $legal_entity = ParDataLegalEntity::create($this->getLegalEntityValues());
    $legal_entity->save();

    // We need to create an Organisation first.
    $organisation = ParDataOrganisation::create($this->getOrganisationBusinessValues());
    $organisation->save();

    return [
      'type' => 'coordinator',
      'organisation_name' => 'Test Coordinator',
      'size' => 'Enormous',
      'employees_band' => '10-50',
      'nation' => 'Wales',
      'comments' => $long_string = $this->randomString(1000),
      'premises_mapped' => TRUE,
      'trading_name' => [
        $this->randomString(255),
        $this->randomString(255),
        $this->randomString(255),
      ],
      'field_sic_code' => [
        $sic_code->id(),
      ],
      'field_person' => [
        $person->id(),
      ],
      'field_premises' => [
        $premises->id(),
      ],
      'field_legal_entity' => [
        $legal_entity->id(),
      ],
      'field_coordinator_number' => '12345',
      'field_coordinator_type' => 'Franchise',
      'field_coordinated_businesses' => [
        $organisation->id()
      ],
    ] + $this->getBaseValues();
  }

  public function getOrganisationBusinessValues() {
    // We need to create an SIC Code first.
    $sic_code = ParDataSicCode::create($this->getSicCodeValues());
    $sic_code->save();

    // We need to create a Person first.
    $person = ParDataPerson::create($this->getPersonValues());
    $person->save();

    // We need to create a Premises first.
    $premises = ParDataPremises::create($this->getPremisesValues());
    $premises->save();

    // We need to create a Legal Entity first.
    $legal_entity = ParDataLegalEntity::create($this->getLegalEntityValues());
    $legal_entity->save();

    return [
        'type' => 'business',
        'organisation_name' => 'Test Business',
        'size' => 'Enormous',
        'employees_band' => '10-50',
        'nation' => 'Wales',
        'comments' => $long_string = $this->randomString(1000),
        'premises_mapped' => TRUE,
        'trading_name' => [
          $this->randomString(255),
          $this->randomString(255),
          $this->randomString(255),
        ],
        'sic_code' => [
          $sic_code->id(),
        ],
        'person' => [
          $person->id(),
        ],
        'premises' => [
          $premises->id(),
        ],
        'legal_entity' => [
          $legal_entity->id(),
        ]
      ] + $this->getBaseValues();
  }

  public function getPartnershipValues() {
    // We need to create an Organisation first.
    $organisation = ParDataOrganisation::create($this->getOrganisationBusinessValues());
    $organisation->save();

    // We need to create a Authority first.
    $authority = ParDataAuthority::create($this->getAuthorityValues());
    $authority->save();

    // We need to create an Advice first.
    $advice = ParDataAdvice::create($this->getAdviceValues());
    $advice->save();

    // We need to create an Inspection Plan first.
    $inspection_plan = ParDataInspectionPlan::create($this->getInspectionPlanValues());
    $inspection_plan->save();

    // We need to create a Regulatory Function first.
    $regulatory_function = ParDataRegulatoryFunction::create($this->getRegulatoryFunctionValues());
    $regulatory_function->save();

    // We need to create a Person first.
    $person_1 = ParDataPerson::create($this->getPersonValues());
    $person_1->save();

    // We need to create a Person first.
    $person_2 = ParDataPerson::create($this->getPersonValues());
    $person_2->save();

    return [
        'type' => 'partnership',
        'partnership_type' => 'Direct Business',
        'partnership_status' => 'Current',
        'about_partnership' => $this->randomString(1000),
        'approved_date' => '2017-06-01',
        'expertise_details' => $this->randomString(1000),
        'cost_recovery' => 'Cost recovery from partnership',
        'reject_comment' => $this->randomString(1000),
        'revocation_source' => 'RD Executive',
        'revocation_date' => '2017-07-01',
        'revocation_reason' => $this->randomString(1000),
        'authority_change_comment' => $this->randomString(1000),
        'organisation_change_comment' => $this->randomString(1000),
        'terms_organisation_agreed' => TRUE,
        'terms_authority_agreed' => TRUE,
        'coordinator_suitable' => TRUE,
        'partnership_info_agreed_authority' => TRUE,
        'partnership_info_agreed_business' => TRUE,
        'written_summary_agreed' => TRUE,
        'organisation' => [
          $organisation->id(),
        ],
        'authority' => [
          $authority->id(),
        ],
        'advice' => [
          $advice->id(),
        ],
        'inspection_plan' => [
          $inspection_plan->id(),
        ],
        'regulatory_function' => [
          $regulatory_function->id(),
        ],
        'authority_person' => [
          $person_1->id(),
        ],
        'organisation_person' => [
          $person_2->id(),
        ]
      ] + $this->getBaseValues();
  }

  public function getPersonValues() {
    return [
      'type' => 'person',
      'salutation' => 'Mrs',
      'first_name' => 'Smith',
      'last_name' => 'Smith',
      'job_title' => 'Senior Telephone Sanitisation Engineer',
      'work_phone' => '01723456789',
      'mobile_phone' => '0777777777',
      'email' => 'abcdefghijklmnopqrstuvwxyz@example.com',
      'communication_email' => TRUE,
      'communication_phone' => TRUE,
      'communication_mobile' => TRUE,
      'communication_notes' => $this->randomString(1000),
      'user_account' => [
        $this->account->id(),
      ]
    ] + $this->getBaseValues();
  }

  public function getPremisesValues() {
    return [
      'type' => 'premises',
      'address' => [
        'country_code' => 'GB',
        'address_line1' => '1 High St',
        'address_line2' => 'London',
        'locality' => 'Greater London',
        'administrative_area' => 'GB-GB',
        'postal_code' => 'N11AA',
      ],
      'nation' => 'Wales',
      'uprn' => '10012330060',
    ] + $this->getBaseValues();
  }

  public function getRegulatoryFunctionValues() {
    return [
      'type' => 'regulatory_function',
      'function_name' => 'Health and Safety',
    ] + $this->getBaseValues();
  }

  public function getSicCodeValues() {
    return [
      'type' => 'sic_code',
      'sic_code' => '012345',
      'description' => 'This is an example SIC Code.'
    ] + $this->getBaseValues();
  }
}
