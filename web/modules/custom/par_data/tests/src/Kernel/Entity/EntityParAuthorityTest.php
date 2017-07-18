<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;

/**
 * Tests PAR Authority entity.
 *
 * @group PAR Data
 */
class EntityParAuthorityTest extends EntityKernelTestBase {

  static $modules = ['trance', 'par_data'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    parent::setUp();

    // Set up schema for par_data.
    $this->installEntitySchema('par_data_authority');
    // Config already installed so we don't need to do this.
    // But if it changes we may need to update.
    // $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataAuthorityType::create([
      'id' => 'authority',
      'label' => 'Authority',
    ]);
    $type->save();
  }

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testEntityValidate() {
    $this->createUser();
    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => 'Test Authority',
      'authority_type' => 'Local Authority',
      'nation' => 'Wales',
      'ons_code' => '123456',
    ]);
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Authority entity.');
  }

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testRequiredFields() {
    $this->createUser();
    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => '',
      'authority_type' => '',
      'nation' => '',
      'ons_code' => '',
    ]);
    $violations = $entity->validate()->getByFields([
      'authority_name',
      'authority_type',
      'nation',
      'ons_code',
    ]);
    $this->assertEqual(count($violations), 4, 'Required fields cannot be empty.');
    $this->assertEqual($violations[0]->getMessage()->render(), 'This value should not be null.', 'These fields are required.');
  }

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => $this->randomString(501),
      'authority_type' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'ons_code' => $this->randomString(256),
    ]);
    $violations = $entity->validate()->getByFields([
      'authority_name',
      'authority_type',
      'nation',
      'ons_code',
    ]);
    $this->assertEqual(count($violations), 4, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[0]->getMessage()->render(), t('%field: may not be longer than 500 characters.', ['%field' => 'Name']), 'The length of the Name field is correct..');
    $this->assertEqual($violations[1]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Authority Type']), 'The length of the Authority Type field is correct.');
  }

  /**
   * Test to create and save a PAR Authority entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => 'Test Authority',
      'authority_type' => 'Local Authority',
      'nation' => 'Wales',
      'ons_code' => '123456',
    ]);
    $this->assertTrue($entity->save(), 'PAR Authority entity saved correctly.');
  }
}
