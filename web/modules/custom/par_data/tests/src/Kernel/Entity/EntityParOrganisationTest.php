<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

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
    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Organisation entity of type Business.');
  }

  /**
   * Test all organisation fields exist.
   */
  public function testOrganisationFieldsExist() {
    $values = $this->getOrganisationValues();
    $entity = ParDataOrganisation::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Organisation.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate an organisation entity.
   */
  public function testOrganisationRequiredFields() {
    $this->createUser();

    $values = [
      'organisation_name' => '',
      'size' => '',
      'employees_band' => '',
      'nation' => '',
      'comments' => '',
      'trading_name' => '',
      'coordinator_type' => '',
      'coordinator_number' => '',
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate an organisation entity.
   */
  public function testOrganisationRequiredLengthFields() {
    $this->createUser();

    $values = [
      'organisation_name' => $this->randomString(501),
      'size' => $this->randomString(256),
      'employees_band' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'premises_mapped' => $this->randomString(10),
      'coordinator_number' => $this->randomString(256),
      'coordinator_type' => $this->randomString(256),
      'trading_name' => [
        $this->randomString(501),
      ]
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $this->createUser();

    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $this->assertTrue($entity->save(), 'PAR Organisation entity saved correctly.');
  }
}
