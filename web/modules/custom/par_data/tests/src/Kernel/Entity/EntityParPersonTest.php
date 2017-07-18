<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;

/**
 * Tests PAR Person entity.
 *
 * @group PAR Data
 */
class EntityParPersonTest extends EntityKernelTestBase {

  static $modules = ['trance', 'par_data'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    parent::setUp();

    // Set up schema for par_data.
    $this->installEntitySchema('par_data_person');
    // Config already installed so we don't need to do this.
    // But if it changes we may need to update.
    // $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataPersonType::create([
      'id' => 'person',
      'label' => 'Person',
    ]);
    $type->save();
  }

  /**
   * Test to validate a PAR Person entity.
   */
  public function testEntityValidate() {
    $this->createUser();
    $entity = ParDataPerson::create([
      'type' => 'person',
      'name' => 'test',
      'uid' => 1,
      'salutation' => 'Mrs',
      'person_name' => 'Jo Smith',
      'work_phone' => '01723456789',
      'mobile_phone' => '0777777777',
      'email' => 'abcdefghijklmnopqrstuvwxyz@example.com'
    ]);
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Person entity.');
  }

  /**
   * Test to validate a PAR Person entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    $entity = ParDataPerson::create([
      'type' => 'person',
      'name' => 'test',
      'uid' => 1,
      'salutation' => 'Mrs',
      'person_name' => $this->randomString(501),
      'work_phone' => $this->randomString(256),
      'mobile_phone' => $this->randomString(256),
      'email' => $this->randomString(501)
    ]);
    $violations = $entity->validate()->getByFields([
      'salutation',
      'person_name',
      'work_phone',
      'mobile_phone',
      'email',
    ]);
    $this->assertEqual(count($violations), 4, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[3]->getMessage()->render(), t('%field: may not be longer than 500 characters.', ['%field' => 'E-mail']), 'The length of the E-mail field is correct..');
    $this->assertEqual($violations[1]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Work Phone']), 'The length of the Work Phone field is correct.');
  }

  /**
   * Test to create and save a PAR Person entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataPerson::create([
      'type' => 'person',
      'name' => 'test',
      'uid' => 1,
      'person_name' => 'Test Person',
      'person_type' => 'Local Person',
      'nation' => 'Wales',
      'ons_code' => '123456',
    ]);
    $this->assertTrue($entity->save(), 'PAR Person entity saved correctly.');
  }
}
