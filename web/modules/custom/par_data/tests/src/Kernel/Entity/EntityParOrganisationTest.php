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
   * Test to validate a PAR Authority entity.
   */
  public function testEntityValidate() {
    $this->createUser();
    $string = $this->randomString(255);
    $long_string = $this->randomString(1000);

    $this->createUser();
    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'title' => 'test',
      'uid' => 1,
      'name' => 'Test Business',
      'size' => 'Enormous',
      'employees_band' => '10-50',
      'nation' => 'Wales',
      'comments' => $long_string,
      'premises_mapped' => TRUE,
      'trading_name' => [
        $string,
        $string,
        $string,
      ],
    ]);
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Organisation entity of type Business.');
  }

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testRequiredFields() {
    $this->createUser();
    $string = $this->randomString(256);
    $long_string = $this->randomString(1000);

    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'title' => 'test',
      'uid' => 1,
      'name' => $this->randomString(501),
      'size' => $string,
      'employees_band' => $string,
      'nation' => $string,
      'comments' => $long_string,
      'premises_mapped' => $this->randomString(10),
      'trading_name' => [
        $string,
        $string,
        $string,
      ],
    ]);
    $violations = $entity->validate()->getByFields([
      'name',
      'size',
      'employees_band',
      'nation',
      'comments',
      'premises_mapped',
      'trading_name',
    ]);
    $this->assertEqual(count($violations), 7, 'Required fields cannot be empty.');
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $string = $this->randomString(255);
    $long_string = $this->randomString(1000);

    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'title' => 'test',
      'uid' => 1,
      'name' => 'Test Business',
      'size' => 'Enormous',
      'employees_band' => '10-50',
      'nation' => 'Wales',
      'comments' => $long_string,
      'premises_mapped' => TRUE,
      'trading_name' => [
        $string,
        $string,
        $string,
      ],
    ]);
    $this->assertTrue($entity->save(), 'PAR Organisation entity saved correctly.');
  }
}
