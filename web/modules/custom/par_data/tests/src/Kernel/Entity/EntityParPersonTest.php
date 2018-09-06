<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\par_data\Entity\ParDataPersonType;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Person entity.
 *
 * @group PAR Data
 */
class EntityParPersonTest extends ParDataTestBase {

  /**
   * Test to validate a PAR Person entity.
   */
  public function testEntityValidate() {
    $entity = ParDataPerson::create($this->getPersonValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Person entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testPersonFieldsExist() {
    $values = $this->getPersonValues();
    $entity = ParDataPerson::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Person.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate a people entity.
   */
  public function testPeopleRequiredFields() {
    $values = [
      'first_name' => '',
      'last_name' => '',
      'work_phone' => '',
      'email' => '',
    ];

    $entity = ParDataPerson::create($values + $this->getPersonValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate an authority entity.
   */
  public function testPersonRequiredLengthFields() {
    $values = [
      'salutation' => $this->randomString(256),
      'first_name' => $this->randomString(501),
      'last_name' => $this->randomString(501),
      'job_title' => $this->randomString(501),
      'work_phone' => $this->randomString(256),
      'mobile_phone' => $this->randomString(256),
      'email' => $this->randomString(501),
    ];

    $entity = ParDataPerson::create($values + $this->getPersonValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Person entity.
   */
  public function testEntityCreate() {
    $entity = ParDataPerson::create($this->getPersonValues());
    $this->assertTrue($entity->save(), 'PAR Person entity saved correctly.');
  }
}
