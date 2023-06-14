<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\par_forms\Plugin\ParForm\ParOrganisationSuggestionForm;
use Drupal\par_member_upload_flows\ParMemberCsvHandler;
use Drupal\par_partnership_flows\Form\ParPartnershipFlowsContactSuggestionForm;
use Drupal\par_partnership_flows\Form\ParPartnershipFlowsOrganisationSuggestionForm;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;
use Drupal\user\Entity\User;

/**
 * Tests case insensitivity for entity queries.
 *
 * Most of the entity queries where case sensitivity matters
 * is when looking up comparable organisations, legal entities or people.
 *
 * @see https://regulatorydelivery.atlassian.net/wiki/spaces/PA/pages/2315288610/PAR-1765+Resolve+case+insensitivity+in+postgres
 *
 * @group PAR Data
 */
class EntityQueryCaseInsensitivityTest extends ParDataTestBase {

  /** @var \Drupal\par_data\Entity\ParDataOrganisation[] */
  protected $organisations = [];

  /** @var \Drupal\par_data\Entity\ParDataLegalEntity[] */
  protected $legalEntities = [];

  /** @var \Drupal\par_data\Entity\ParDataPerson[] */
  protected $people = [];

  /** @var \Drupal\user\Entity\User */
  protected $primaryAccount;

  /** @var \Drupal\Core\Entity\EntityStorageInterface[] */
  protected $storage = [];

  /**
   * Set up the required entities for testing.
   *
   * User 1: `Sally.Field@example.com`
   *
   * Organisation 1:
   *  ├── Organisation name `WillowBrook Nursing Services`
   *  ├── Trading name `WillowBrook Care`
   *  │
   *  ├── Org Contact 1
   *  │    └── Email `Sally.Field@example.com`
   *  │    └── First name `SallyTest`
   *  │    └── Last name `FieldTest`
   *  │
   *  └── Legal Entity 1
   *       └── Registered name `WillowBrook Nursing Services Ltd`
   *       └── Registered number `EG123456`
   *
   * Organisation 2:
   *  ├── Organisation name `WILLOWBROOK NURSING SERVICES`
   *  ├── Trading name `WILLOWBROOK CARE`
   *  │
   *  ├── Org Contact 2
   *  │    └── Email `SALLY.FIELD@example.com`
   *  │    └── First name `SALLYTEST`
   *  │    └── Last name `FIELDTEST`
   *  │
   *  └── Legal Entity 2
   *       └── Registered name `WILLOWBROOK NURSING SERVICES LTD`
   *       └── Registered number `EG123456`
   *
   * Organisation 3:
   *  ├── Organisation name `willowbrook nursing services`
   *  ├── Trading name `willowbrook care`
   *  │
   *  ├── Org Contact 3
   *  │    └── Email `sally.field@example.com`
   *  │    └── First name `sallytest`
   *  │    └── Last name `fieldtest`
   *  │
   *  └── Legal Entity 3
   *       └── Registered name `willowbrook nursing services ltd`
   *       └── Registered number `EG123456`
   */
  protected function setUp() {
    parent::setUp();

    // Create a single user account.
    $this->primaryAccount = $this->createUser([
        'mail' => "Sally.Field@example.com",
        'name' => "SallyFieldTest"
      ],
      $this->permissions);

    // Create 3 similar contact records that vary on case.
    $people_values = [
      'sentence' => [
        'email' => "Sally.Field@example.com",
        'first_name' => "SallyTest",
        'last_name' => "FieldTest",
        'job_title' => 'Senior Test Engineer',
        'field_user_account' => [$this->primaryAccount->id()]
      ],
      'upper' => [
        'email' => "SALLY.FIELD@example.com",
        'first_name' => "SALLYTEST",
        'last_name' => "FIELDTEST",
        'job_title' => 'SENIOR TEST ENGINEER',
        'field_user_account' => []
      ],
      'lower' => [
        'email' => "sally.field@example.com",
        'first_name' => "sallytest",
        'last_name' => "fieldtest",
        'job_title' => 'senior test engineer',
        'field_user_account' => []
      ],
      'mismatching' => [
        'email' => "sally.field.mismatch@example.com",
        'first_name' => "sallymismatchtest",
        'last_name' => "fieldmismatchtest",
        'job_title' => 'Senior Mismatch Test Engineer',
        'field_user_account' => []
      ],
    ];
    foreach ($people_values as $key => $values) {
      $this->people[$key] = ParDataPerson::create($this->getPersonValues($values));
      $this->people[$key]->save();
    }

    // Create 3 similar legal entities that vary on case.
    $legal_values = [
      'sentence' => [
        'registry' => "internal",
        'registered_name' => "WillowBrook Nursing Services Ltd",
        'registered_number' => "EG123456",
        'legal_entity_type' => "other",
      ],
      'upper' => [
        'registry' => "internal",
        'registered_name' => "WILLOWBROOK NURSING SERVICES LTD",
        'registered_number' => "EG234567",
        'legal_entity_type' => "other",
      ],
      'lower' => [
        'registry' => "internal",
        'registered_name' => "willowbrook nursing services ltd",
        'registered_number' => "EG345678",
        'legal_entity_type' => "other",
      ],
      'mismatch' => [
        'registry' => "internal",
        'registered_name' => "WillowBrook Mismatched Nursing Services Ltd",
        'registered_number' => "EG999999",
        'legal_entity_type' => "other",
      ]
    ];
    foreach ($legal_values as $key => $values) {
      $this->legalEntities[$key] = ParDataLegalEntity::create($this->getLegalEntityValues($values));
      $this->legalEntities[$key]->save();
    }

    // Create 3 similar organisations that vary on case.
    $organisation_values = [
      'sentence' => [
        'organisation_name' => "WillowBrook Nursing Services",
        'trading_name' => ["WillowBrook Care"],
        'field_person' => [$this->people['sentence']->id()],
      ],
      'upper' => [
        'organisation_name' => "WILLOWBROOK NURSING SERVICES",
        'trading_name' => ["WILLOWBROOK CARE"],
        'field_person' => [$this->people['upper']->id()],
      ],
      'lower' => [
        'organisation_name' => "willowbrook nursing services",
        'trading_name' => ["willowbrook care"],
        'field_person' => [$this->people['lower']->id()],
      ],
      'mismatch' => [
        'organisation_name' => "WillowBrook Mismatched Nursing Services",
        'trading_name' => ["WillowBrook Mismatched Care"],
        'field_person' => [$this->people['mismatching']->id()],
      ],
      'incorrect' => [
        'organisation_name' => "IncorrectWillowBrook Nursing Services",
        'trading_name' => ["IncorrectWillowBrook Care"],
        'field_person' => [$this->people['mismatching']->id()],
      ],
    ];
    foreach ($organisation_values as $key => $values) {
      $this->organisations[$key] = ParDataOrganisation::create($this->getOrganisationValues($values));
      $this->organisations[$key]->save();
    }

    $this->storage['par_data_person'] = $this->entityTypeManager->getStorage('par_data_person');
    $this->storage['par_data_organisation'] = $this->entityTypeManager->getStorage('par_data_organisation');
    $this->storage['par_data_legal_entity'] = $this->entityTypeManager->getStorage('par_data_legal_entity');
  }

  protected function getQuery($type, $conjunction = 'AND', $access_check = FALSE) {
    return $this->storage[$type]->getQuery($conjunction)
      ->accessCheck($access_check);
  }

  /**
   * Test a case insensitive query for similar people by email.
   *
   * @group casesensitivity
   *
   * @see ParDataManager::getUserPeople().
   * @see ParPartnershipFlowsContactSuggestionForm::buildForm().
   */
  public function testInsensitiveEmail() {
    $query = $this->getQuery('par_data_person', 'OR');

    // This is a condensed version of the query that tests the key case-sensitive criteria.
    $query->condition('email', $this->primaryAccount->get('mail')->getString());

    $results = $query->execute();

    // Check that all 3 contact records were found.
    $this->assertCount(3, $results, t('Getting similar contact records by email found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for similar people by name.
   *
   * @group casesensitivity
   *
   * @see ParDataManager::getUserPeople().
   * @see ParPartnershipFlowsContactSuggestionForm::buildForm().
   */
  public function testInsensitiveContactName() {
    $query = $this->getQuery('par_data_person');

    // This is a condensed version of the query that tests the key case-sensitive criteria.
    $group = $query
      ->andConditionGroup()
      ->condition('first_name', "SallyTest")
      ->condition('last_name', "FieldTest");

    $results = $query->condition($group)
      ->execute();

    // Check that all 3 contact records were found.
    $this->assertCount(3, $results, t('Getting similar contact records by name found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for similar organisations by name.
   *
   * @group casesensitivity
   *
   * @see ParOrganisationSuggestionForm::loadData().
   * @see ParMemberCsvHandler::normalize().
   * @see ParPartnershipFlowsOrganisationSuggestionForm::buildForm().
   */
  public function testInsensitiveOrgName() {
    $query = $this->getQuery('par_data_organisation', 'OR');

    // The following condition group is used to match whole words, not partial words.
    $organisation_name = "WillowBrook Nursing Services";
    $group = $query
      ->orConditionGroup()
      ->condition('organisation_name', "$organisation_name", '=')
      ->condition('organisation_name', "%$organisation_name%", 'CONTAINS')
      ->condition('organisation_name', "$organisation_name ", 'STARTS_WITH')
      ->condition('organisation_name', " $organisation_name", 'ENDS_WITH');

    $results = $query->condition($group)
      ->execute();

    // Check that all 3 organisations were found.
    $this->assertCount(3, $results, t('Getting similar organisations by name found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for an exact match by organisation name.
   *
   * The '=' operator is the most important to test in isolation.
   *
   * @group casesensitivity
   *
   * @see ParOrganisationSuggestionForm::loadData().
   * @see ParMemberCsvHandler::normalize().
   * @see ParPartnershipFlowsOrganisationSuggestionForm::buildForm().
   */
  public function testInsensitiveExactOrgName() {
    $query = $this->getQuery('par_data_organisation', 'OR');

    // In some cases simpler conditions are used where matching an exact name is important.
    $organisation_name = "WillowBrook Nursing Services";
    $results = $query->condition('organisation_name', "$organisation_name", '=')
        ->execute();

    // Check that all 3 organisations were found.
    $this->assertCount(3, $results, t('Getting exact organisation matches by name found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for similar organisations by partial name.
   *
   * @group casesensitivity
   *
   * @see ParOrganisationSuggestionForm::loadData().
   * @see ParMemberCsvHandler::normalize().
   * @see ParPartnershipFlowsOrganisationSuggestionForm::buildForm().
   */
  public function testInsensitivePartialOrgName() {
    $query = $this->getQuery('par_data_organisation', 'OR');

    // The following condition group is used to match whole words (not partial words)
    //  e.g. searching for "WillowBrook Care":
    // 'WillowBrook'            true
    // 'Test WillowBrook Care'  true
    // 'TestWillowBrook Care'   false
    // 'WillowBrook Test Care'  false
    $organisation_name = "WillowBrook";
    $group = $query
      ->orConditionGroup()
      ->condition('organisation_name', "%$organisation_name%", 'CONTAINS')
      ->condition('organisation_name', "$organisation_name ", 'STARTS_WITH')
      ->condition('organisation_name', " $organisation_name", 'ENDS_WITH');

    $results = $query->condition($group)
      ->execute();

    // Check that all partial name matches were ignored.
    $this->assertCount(4, $results, t('Getting similar organisations by a partial name found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for similar organisations by trading name.
   *
   * @group casesensitivity
   *
   * @see ParOrganisationSuggestionForm::loadData().
   * @see ParMemberCsvHandler::normalize().
   * @see ParPartnershipFlowsOrganisationSuggestionForm::buildForm().
   */
  public function testInsensitiveOrgTradingName() {
    $query = $this->getQuery('par_data_organisation', 'OR');

    // The following condition group is used to match whole words (not partial words)
    $trading_name = "WillowBrook Care";
    $group = $query
      ->orConditionGroup()
      ->condition('trading_name', "$trading_name", '=')
      ->condition('trading_name', "%$trading_name%", 'CONTAINS')
      ->condition('trading_name', "$trading_name ", 'STARTS_WITH')
      ->condition('trading_name', " $trading_name", 'ENDS_WITH');

    $results = $query->condition($group)
      ->execute();

    // Check that all 3 organisations were found.
    $this->assertCount(3, $results, t('Getting similar organisations by trading name found @results results.',
      ['@results' => count($results)]));
  }

  /**
   * Test a case insensitive query for similar legal entity by name.
   *
   * @group casesensitivity
   *
   * @see ParOrganisationSuggestionForm::loadData().
   * @see ParMemberCsvHandler::normalize().
   * @see ParPartnershipFlowsOrganisationSuggestionForm::buildForm().
   */
  public function testInsensitiveOrgLegalName() {
    $query = $this->getQuery('par_data_legal_entity', 'OR');

    // The following condition group is used to match whole words (not partial words)
    $legal_entity = "WillowBrook Nursing Services Ltd";
    $group = $query
      ->orConditionGroup()
      ->condition('registered_name', "$legal_entity", '=')
      ->condition('registered_name', "%$legal_entity%", 'CONTAINS')
      ->condition('registered_name', "$legal_entity ", 'STARTS_WITH')
      ->condition('registered_name', " $legal_entity", 'ENDS_WITH');

    $results = $query->condition($group)
      ->execute();

    // Check that all 3 legal entities were found.
    $this->assertCount(3, $results, t('Getting similar legal entities by name found @results results.',
      ['@results' => count($results)]));
  }
}
