<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;

/**
 * Tests PAR Organisation entity.
 *
 * @group PAR Data
 */
class EntityParOrganisationTest extends EntityKernelTestBase {

  static $modules = ['trance', 'par_data'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    parent::setUp();

    // Set up schema for par_data.
    $this->installEntitySchema('par_data_organisation');
    // Config already installed so we don't need to do this.
    // But if it changes we may need to update.
    // $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataOrganisationType::create([
      'id' => 'business',
      'label' => 'Business',
    ]);
    $type->save();
  }

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testEntityValidate() {
    $this->createUser();

    $this->createUser();
    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'name' => 'test',
      'uid' => 1,
      'organisation_name' => 'Test Business',
      'size' => 'Enormous',
      'employees_band' => '10-50',
      'nation' => 'Wales',
      'comments' => $this->randomString(1000),
      'premises_mapped' => TRUE,
      'trading_name' => [
        $this->randomString(255),
        $this->randomString(255),
        $this->randomString(255),
      ],
    ]);
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Organisation entity of type Business.');
  }

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'name' => 'test',
      'uid' => 1,
      'organisation_name' => $this->randomString(501),
      'size' => $this->randomString(256),
      'employees_band' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'comments' => $this->randomString(1000),
      'premises_mapped' => $this->randomString(10),
      'trading_name' => [
        $this->randomString(256),
        $this->randomString(256),
        $this->randomString(256),
      ],
    ]);
    $violations = $entity->validate()->getByFields([
      'organisation_name',
      'size',
      'employees_band',
      'nation',
      'comments',
      'premises_mapped',
      'trading_name',
    ]);
    $this->assertEqual(count($violations), 7, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[0]->getMessage()->render(), t('%field: may not be longer than 500 characters.', ['%field' => 'Name']), 'The length of the Name field is correct..');
    $this->assertEqual($violations[1]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Size']), 'The length of the Size field is correct.');
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $this->createUser();

    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'name' => 'test',
      'uid' => 1,
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
    ]);
    $this->assertTrue($entity->save(), 'PAR Organisation entity saved correctly.');
  }
}
