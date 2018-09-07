<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_data\Entity\ParDataPartnershipType;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

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
    $entity = ParDataPartnership::create($this->getCoordinatedPartnershipValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default PAR Partnership entity.');
  }

  /**
   * Test all partnership fields exist.
   */
  public function testParFieldsExist() {
    $values = $this->getCoordinatedPartnershipValues();
    $entity = ParDataPartnership::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Partnership.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate a partnership entity.
   */
  public function testPartnershipRequiredFields() {
    $values = [
      'partnership_type' => '',
      'about_partnership' => '',
    ];

    $entity = ParDataPartnership::create($values + $this->getCoordinatedPartnershipValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate a partnership entity.
   */
  public function testPartnershipRequiredLengthFields() {
    $values = [
      'partnership_type' => $this->randomString(256),
      'partnership_status' => $this->randomString(256),
      'cost_recovery' => $this->randomString(256),
      'revocation_source' => $this->randomString(256),
    ];

    $entity = ParDataPartnership::create($values + $this->getCoordinatedPartnershipValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save a PAR Partnership entity.
   */
  public function testEntityCreate() {
    $entity = ParDataPartnership::create($this->getCoordinatedPartnershipValues());
    $this->assertTrue($entity->save(), 'PAR Partnership entity saved correctly.');
  }
}
