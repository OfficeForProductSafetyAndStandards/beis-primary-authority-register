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
    $this->installConfig('par_data');

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
      'title' => 'test',
      'uid' => 1,
      'name' => 'Test Authority',
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
      'title' => 'test',
      'uid' => 1,
      'name' => '',
      'authority_type' => '',
      'nation' => '',
      'ons_code' => '',
    ]);
    $violations = $entity->validate()->getByFields([
      'name',
      'authority_type',
      'nation',
      'ons_code',
    ]);
    $this->assertEqual(count($violations), 4, 'Required fields cannot be empty.');
    $this->assertEqual($violations[0]->getMessage()->render(), 'This value should not be null.', 'These fields are required.');
  }

  /**
   * Test to create and save a PAR Authority entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'title' => 'test',
      'uid' => 1,
      'name' => 'Test Authority',
      'authority_type' => 'Local Authority',
      'nation' => 'Wales',
      'ons_code' => '123456',
    ]);
    $this->assertTrue($entity->save(), 'PAR Authority entity saved correctly.');
  }
}
