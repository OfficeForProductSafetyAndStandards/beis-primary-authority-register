<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipType;

/**
 * Tests PAR Partnership entity.
 *
 * @group PAR Data
 */
class EntityParPartnershipTest extends ParDataTestBase {

  /**
   * Test to validate a partnership entity.
   */
  public function testEntityValidate() {
    $this->createUser();
    $entity = ParDataPartnership::create($this->getPartnershipValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Partnership entity.');
  }

  /**
   * Test all partnership fields exist.
   */
  public function testParFieldsExist() {
    $values = $this->getPartnershipValues();
    $entity = ParDataPartnership::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Partnership.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate a partnership entity.
   */
  public function testPartnershipRequiredFields() {
    $this->createUser();

    $values = [
      'partnership_type' => '',
      'partnership_status' => '',
      'organisation' => [
        '',
      ],
      'authority' => [
        '',
      ],
      'regulatory_function' => [
        '',
      ],
      'authority_person' => [
        '',
      ],
      'organisation_person' => [
        '',
      ],
    ];

    $entity = ParDataPartnership::create($values + $this->getPartnershipValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate a business entity.
   */
  public function testBusinessRequiredLengthFields() {
    $this->createUser();

    $values = [
      'partnership_type' => $this->randomString(256),
      'partnership_status' => $this->randomString(256),
      'cost_recovery' => $this->randomString(256),
      'revocation_source' => $this->randomString(256),
    ];

    $entity = ParDataPartnership::create($values + $this->getPartnershipValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Partnership entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataPartnership::create($this->getPartnershipValues());
    $this->assertTrue($entity->save(), 'PAR Partnership entity saved correctly.');
  }
}
