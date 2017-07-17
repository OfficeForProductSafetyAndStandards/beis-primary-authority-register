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
    parent::setUp();

    // Set up schema for par_data.
    $this->installEntitySchema('par_data');
    $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataAuthorityType::create([
      'authority' => 'authority',
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
