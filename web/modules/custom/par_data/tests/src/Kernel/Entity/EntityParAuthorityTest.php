<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;

/**
 * Tests PAR Authority entity.
 *
 * @group PAR Data
 */
class EntityParAuthorityTest extends ParDataTestBase {

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testEntityValidate() {
    $this->createUser();

    // Now we can create our
    $entity = ParDataAuthority::create($this->getAuthorityValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Authority entity.');
  }

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testRequiredFields() {
    $this->createUser();

    // Get the defaults.
    $authority_values = $this->getAuthorityValues();

    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => '',
      'authority_type' => '',
      'nation' => '',
      'ons_code' => '',
      'person' => [],
      'regulatory_area' => [],
      'premises' => [],
    ] + $authority_values);
    $violations = $entity->validate()->getByFields([
      'authority_name',
      'authority_type',
      'nation',
      'ons_code',
      'person',
      'regulatory_area',
      'premises',
    ]);
    $this->assertEqual(count($violations->getFieldNames()), 7, 'Required fields cannot be empty.');
    $this->assertEqual($violations[0]->getMessage()->render(), 'This value should not be null.', 'These fields are required.');
  }

  /**
   * Test to validate a PAR Authority entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    // Get the defaults.
    $authority_values = $this->getAuthorityValues();

    $entity = ParDataAuthority::create([
      'type' => 'authority',
      'name' => 'test',
      'uid' => 1,
      'authority_name' => $this->randomString(501),
      'authority_type' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'ons_code' => $this->randomString(256),
    ] + $authority_values);
    $violations = $entity->validate()->getByFields([
      'authority_name',
      'authority_type',
      'nation',
      'ons_code',
    ]);
    $this->assertEqual(count($violations->getFieldNames()), 4, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[0]->getMessage()->render(), t('%field: may not be longer than 500 characters.', ['%field' => 'Authority Name']), 'The length of the Authority Name field is correct.');
    $this->assertEqual($violations[1]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Authority Type']), 'The length of the Authority Type field is correct.');
  }

  /**
   * Test to create and save a PAR Authority entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataAuthority::create($this->getAuthorityValues());
    $this->assertTrue($entity->save(), 'PAR Authority entity saved correctly.');
  }
}
