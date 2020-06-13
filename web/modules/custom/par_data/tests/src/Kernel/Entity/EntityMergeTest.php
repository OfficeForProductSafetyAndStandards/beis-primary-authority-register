<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests the merging of PAR entity references.
 *
 * @group PAR Data
 */
class EntityMergeTest extends ParDataTestBase {

  /** @var \Drupal\par_data\Entity\ParDataPartnership[] */
  protected $partnerships;

  /** @var \Drupal\par_data\Entity\ParDataOrganisation[] */
  protected $organisations;

  /** @var \Drupal\par_data\Entity\ParDataAuthority[] */
  protected $authorities;

  /** @var \Drupal\par_data\Entity\ParDataPerson[] */
  protected $people;

  /**
   * Set up the required entities for testing.
   *
   * Authority 1:
   *  ├── Auth Contact 1
   *  ├── Auth Contact 2
   *  │
   *  ├── Partnership 1
   *  │    └── Auth Contact 1
   *  ├── Partnership 2
   *  │    └── Auth Contact 1
   *  └── Partnership 3
   *       └── Auth Contact 1
   *
   * Authority 2:
   *  ├── Auth Contact 3
   *  ├── Auth Contact 4
   *  │
   *  ├── Partnership 4
   *  │    └── Auth Contact 4
   *  ├── Partnership 5
   *  │    └── Auth Contact 4
   *  └── Partnership 6
   *       └── Auth Contact 4
   *
   * Authority 3:
   *  ├── Auth Contact 5
   *  ├── Auth Contact 6
   *  │
   *  ├── Partnership 7
   *  │    ├── Auth Contact 5
   *  │    └── Auth Contact 6
   *  ├── Partnership 8
   *  │    ├── Auth Contact 5
   *  │    └── Auth Contact 6
   *  └── Partnership 9
   *       ├── Auth Contact 5
   *       └── Auth Contact 6
   *
   * Organisation 1:
   *  ├── Org Contact 7
   *  ├── Org Contact 8
   *  │
   *  ├── Partnership 1
   *  │    └── Org Contact 7
   *  ├── Partnership 4
   *  │    └── Org Contact 7
   *  └── Partnership 7
   *       └── Org Contact 7
   *
   * Organisation 2:
   *  ├── Org Contact 9
   *  ├── Org Contact 10
   *  │
   *  ├── Partnership 2
   *  │    └── Org Contact 10
   *  ├── Partnership 5
   *  │    └── Org Contact 10
   *  └── Partnership 8
   *       └── Org Contact 10
   *
   * Organisation 3:
   *  ├── Org Contact 11
   *  ├── Org Contact 12
   *  │
   *  ├── Partnership 3
   *  │    ├── Org Contact 11
   *  │    └── Org Contact 12
   *  ├── Partnership 6
   *  │    ├── Org Contact 11
   *  │    └── Org Contact 12
   *  └── Partnership 9
   *       ├── Org Contact 11
   *       └── Org Contact 12
   */
  protected function setUp() {
    parent::setUp();

    // Create 12 people with two different email addresses.
    $first_email = $this->randomMachineName(20) . '@example.com';
    $second_email = $this->randomMachineName(20) . '@example.com';
    for ($i=1; $i<=12; $i++) {
      // Odd indexes should _not_ have a user account associated with them.
      $default_values = $i % 2 == 0 ? [] : ['field_user_account' => []];

      // The first six people should have the email address $first_email
      if ($i <= 6) {
        $person_values = $this->getPersonValues($default_values + [
          'first_name' => $i,
          'email' => $first_email,
        ]);
      }
      // The second six people should have the email address $second_email
      else {
        $person_values = $this->getPersonValues($default_values + [
          'first_name' => $i,
          'email' => $second_email,
        ]);
      }
      $this->people[$i] = ParDataPerson::create($person_values);
      $this->people[$i]->save();
    }

    // Create 3 authorities.
    for ($i=1; $i<=3; $i++) {
      switch ($i) {
        case 1:
          $authority_values = $this->getAuthorityValues([
            'authority_name' => 'Test Authority 1',
            'field_person' => [$this->people[1], $this->people[2]]
          ]);

          break;
        case 2:
          $authority_values = $this->getAuthorityValues([
            'authority_name' => 'Test Authority 2',
            'field_person' => [$this->people[3], $this->people[4]]
          ]);

          break;
        case 3:
          $authority_values = $this->getAuthorityValues([
            'authority_name' => 'Test Authority 3',
            'field_person' => [$this->people[5], $this->people[6]]
          ]);

          break;
      }
      $this->authorities[$i] = ParDataAuthority::create($authority_values);
      $this->authorities[$i]->save();
    }

    // Create 3 organisations.
    for ($i=1; $i<=3; $i++) {
      switch ($i) {
        case 1:
          $organisation_values = $this->getOrganisationValues([
            'organisation_name' => 'Test Organisation 1',
            'field_person' => [$this->people[7], $this->people[8]]
          ]);

          break;
        case 2:
          $organisation_values = $this->getOrganisationValues([
            'organisation_name' => 'Test Organisation 2',
            'field_person' => [$this->people[9], $this->people[10]]
          ]);

          break;
        case 3:
          $organisation_values = $this->getOrganisationValues([
            'organisation_name' => 'Test Organisation 3',
            'field_person' => [$this->people[11], $this->people[12]]
          ]);

          break;
      }
      $this->organisations[$i] = ParDataOrganisation::create($organisation_values);
      $this->organisations[$i]->save();
    }

    // Create 9 partnerships.
    for ($i=1; $i<=9; $i++) {
      // Add the appropriate authority values.
      if ($i <= 3) {
        $partnership_values = [
          'field_authority' => [$this->authorities[1]],
          'field_authority_person' => [$this->people[1]],
        ];
      }
      elseif ($i <= 6) {
        $partnership_values = [
          'field_authority' => [$this->authorities[2]],
          'field_authority_person' => [$this->people[4]],
        ];
      }
      else {
        $partnership_values = [
          'field_authority' => [$this->authorities[3]],
          'field_authority_person' => [$this->people[5], $this->people[6]],
        ];
      }

      // Add the appropriate organisation values.
      switch ($i % 3) {
        case 1:
          $partnership_values += [
            'field_organisation' => [$this->organisations[1]],
            'field_organisation_person' => [$this->people[1]],
          ];

          break;
        case 2:
          $partnership_values += [
            'field_organisation' => [$this->organisations[2]],
            'field_organisation_person' => [$this->people[10]],
          ];

          break;
        case 0:
          $partnership_values += [
            'field_organisation' => [$this->organisations[3]],
            'field_organisation_person' => [$this->people[11], $this->people[12]],
          ];

          break;
      }

      // Create the partnership.
      $partnership_values = $this->getDirectPartnershipValues($partnership_values);
      $this->partnerships[$i] = ParDataPartnership::create($partnership_values);
      $this->partnerships[$i]->save();
    }
  }

  /**
   * Test merging of authority users.
   */
  public function testAuthorityContactsMerge() {
    // Use the first person.
    $person_mail = $this->people[1]->get('email')->getString();

    // Get the storage for this person.
    $storage = \Drupal::entityTypeManager()->getStorage('par_data_person');

    // Assert that 6 people records were created.
    $original_entities = $storage->loadByProperties(['email'=> $person_mail]);
    $this->assertCount(6, $original_entities, t('6 contact records have been created for First Person.'));

    // Assert that each of the authorities has the correct number of records.
    for ($i=1; $i<=3; $i++) {
      $original_count = $this->authorities[$i]->get('field_person')->count();
      $this->assertEqual($original_count, 2, t('Authority @i has 2 contact records.', ['@i' => $i]));

      // Also assert that both contact records are legitimate entity references,
      // it's possible that a reference to a deleted record might remain.
      $original_authority_entities = $this->authorities[$i]->get('field_person')->referencedEntities();
      $this->assertCount(2, $original_authority_entities, t('Authority @i has 2 contact references.', ['@i' => $i]));
    }

    // Assert that each of the partnerships has the correct number of records.
    for ($i=1; $i<=9; $i++) {
      $count = ($i <= 6) ? 1 : 2;
      // Assert that each of the authorities has the correct number of records.
      $original_count = $this->partnerships[$i]->get('field_authority_person')->count();
      $this->assertEqual($original_count, $count, t('Partnership @i has @count contact records.', ['@i' => $i, '@count' => $count]));

      // Also assert that both contact records are legitimate entity references,
      // it's possible that a reference to a deleted record might remain.
      $original_authority_entities = $this->partnerships[$i]->get('field_authority_person')->referencedEntities();
      $this->assertCount($count, $original_authority_entities, t('Partnership @i has @count contact references.', ['@i' => $i, '@count' => $count]));
    }

    // Check that the right number of matching people are found.
    $people = $this->people[1]->getAllRelatedPeople();
    $this->assertCount(6, $people, t('There are 6 people that share the same email address.'));

    // Merge the contact records.
    // All authority contacts 2-6 should now be deleted and merged into contact 1.
    for ($i=2; $i<=6; $i++) {
      $this->people[1]->merge($this->people[$i], FALSE);
    }
    $this->people[1]->save();

    // Re-load all the entities that may or may not have been updated since merging.
    for ($i=1; $i<=3; $i++) {
      $this->authorities[$i] = ParDataAuthority::load($this->authorities[$i]->id());
      $this->organisations[$i] = ParDataOrganisation::load($this->organisations[$i]->id());
    }
    for ($i=1; $i<=9; $i++) {
      $this->partnerships[$i] = ParDataPartnership::load($this->partnerships[$i]->id());
    }

    // Assert that all 6 records were merged into one.
    $merged_entities = $storage->loadByProperties(['email'=> $person_mail]);
    $this->assertCount(1, $merged_entities, t('Only 1 contact record now exists for First Person, the others have been merged.'));

    // Assert that each of the authorities has the correct number of records.
    for ($i=1; $i<=3; $i++) {
      $original_count = $this->authorities[$i]->get('field_person')->count();
      $this->assertEqual($original_count, 1, t('Authority @i has 1 contact record.', ['@i' => $i]));

      // Also assert that both contact records are legitimate entity references,
      // it's possible that a reference to a deleted record might remain.
      $original_authority_entities = $this->authorities[$i]->get('field_person')->referencedEntities();
      $this->assertCount(1, $original_authority_entities, t('Authority @i has 1 contact references.', ['@i' => $i]));
    }

    // Assert that each of the partnerships has the correct number of records.
    for ($i=1; $i<=9; $i++) {
      // Assert that each of the authorities has the correct number of records.
      $original_count = $this->partnerships[$i]->get('field_authority_person')->count();
      $this->assertEqual($original_count, 1, t('Partnership @i has 1 contact record.', ['@i' => $i]));

      // Also assert that both contact records are legitimate entity references,
      // it's possible that a reference to a deleted record might remain.
      $original_authority_entities = $this->partnerships[$i]->get('field_authority_person')->referencedEntities();
      $this->assertCount(1, $original_authority_entities, t('Partnership @i has 1 contact references.', ['@i' => $i]));
    }
  }
}
