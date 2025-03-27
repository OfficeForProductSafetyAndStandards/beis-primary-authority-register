<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\par_data\Entity\ParDataOrganisation;
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
    $this->assertEquals(0, count($violations->getFieldNames()), 'No violations when validating a default PAR Organisation entity of type Business.');
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
    $values = [
      'organisation_name' => '',
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEquals(
      count($values),
      count($violations->getFieldNames()),
      t(
        'Field values are required for %fields.',
        ['%fields' => implode(', ', $violations->getFieldNames())]
      )->render()
    );
  }

  /**
   * Test to validate an organisation entity.
   */
  public function testOrganisationRequiredLengthFields() {
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
      ],
    ];

    $entity = ParDataOrganisation::create($values + $this->getOrganisationValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEquals(count($values), count($violations->getFieldNames()), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $this->assertTrue($entity->save() === SAVED_NEW, 'PAR Organisation entity saved correctly.');
  }

}
