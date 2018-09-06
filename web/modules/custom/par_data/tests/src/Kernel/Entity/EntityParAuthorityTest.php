<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataAuthority;
use Drupal\par_data\Entity\ParDataAuthorityType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Authority entity.
 *
 * @group PAR Data
 */
class EntityParAuthorityTest extends ParDataTestBase {

  /**
   * Test to validate an authority entity.
   */
  public function testEntityValidate() {
    $entity = ParDataAuthority::create($this->getAuthorityValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Authority entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testAuthorityFieldsExist() {
    $values = $this->getAuthorityValues();
    $entity = ParDataAuthority::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Authority.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate an authority entity.
   */
  public function testAuthorityRequiredFields() {
    $values = [
      'authority_name' => '',
      'authority_type' => '',
      'nation' => '',
      'ons_code' => '',
      'comments' => '',
    ];

    $entity = ParDataAuthority::create($values + $this->getAuthorityValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate an authority entity.
   */
  public function testAuthorityRequiredLengthFields() {
    $values = [
      'authority_name' => $this->randomString(501),
      'authority_type' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'ons_code' => $this->randomString(256),
    ];

    $entity = ParDataAuthority::create($values + $this->getAuthorityValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $entity = ParDataAuthority::create($this->getAuthorityValues());
    $this->assertTrue($entity->save(), 'PAR Authority entity saved correctly.');
  }
}
