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
class EntityParOrganisationTest extends ParDataTestBase {

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testEntityValidate() {
    $entity = ParDataOrganisation::create($this->getOrganisationBusinessValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Organisation entity of type Business.');

    $entity = ParDataOrganisation::create($this->getOrganisationCoordinatorValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Organisation entity of type Coordinator.');
  }

  /**
   * Test all business fields exist.
   */
  public function testBusinessFieldsExist() {
    $values = $this->getOrganisationBusinessValues();
    $entity = ParDataOrganisation::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Business.', ['%field' => $field]));
    }
  }

  /**
   * Test all coordinator fields exist.
   */
  public function testCoordinatorFieldsExist() {
    $values = $this->getOrganisationCoordinatorValues();
    $entity = ParDataOrganisation::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Coordinator.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate a business entity.
   * We do not need to test a coordinator because they share
   * the same required fields.
   */
  public function testBusinessRequiredFields() {
    $this->createUser();

    $values = [
      'organisation_name' => '',
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationBusinessValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate a business entity.
   */
  public function testBusinessRequiredLengthFields() {
    $this->createUser();

    $values = [
      'organisation_name' => $this->randomString(501),
      'size' => $this->randomString(256),
      'employees_band' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'premises_mapped' => $this->randomString(10),
      'trading_name' => [
        $this->randomString(256),
      ],
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationBusinessValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate a coordinator entity.
   */
  public function testCoordinatorRequiredLengthFields() {
    $this->createUser();

    $values = [
      'organisation_name' => $this->randomString(501),
      'size' => $this->randomString(256),
      'employees_band' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'premises_mapped' => $this->randomString(10),
      'field_coordinator_number' => $this->randomString(256),
      'field_coordinator_type' => $this->randomString(256),
      'trading_name' => [
        $this->randomString(256),
      ]
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationCoordinatorValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $this->createUser();

    $entity = ParDataOrganisation::create($this->getOrganisationBusinessValues());
    $this->assertTrue($entity->save(), 'PAR Organisation entity of type Business saved correctly.');

    $entity = ParDataOrganisation::create($this->getOrganisationCoordinatorValues());
    $this->assertTrue($entity->save(), 'PAR Organisation entity of type Coordinator saved correctly.');
  }
}
