<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataEnforcementNoticeType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Enforcement Notice entity.
 *
 * @group PAR Data
 */
class EntityParEnforcementNoticeTest extends ParDataTestBase {

  /**
   * Test to validate an authority entity.
   */
  public function testEntityValidate() {
    $entity = ParDataEnforcementNotice::create($this->getEnforcementNoticeValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default Par Enforcement Notice entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testEnforcementNoticeFieldsExist() {
    $values = $this->getEnforcementNoticeValues();
    $entity = ParDataEnforcementNotice::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Enforcement Notice.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate an authority entity.
   */
  public function testEnforcementNoticeRequiredFields() {
    $values = [
      'notice_type' => '',
      'notice_date' => '',
      'legal_entity_name' => '',
    ];

    $entity = ParDataEnforcementNotice::create($values + $this->getEnforcementNoticeValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to validate an authority entity.
   */
  public function testEnforcementNoticeRequiredLengthFields() {
    $this->createUser();

    $values = [
      'notice_type' => $this->randomString(256),
      'legal_entity_name' => $this->randomString(501),
    ];

    $entity = ParDataEnforcementNotice::create($values + $this->getEnforcementNoticeValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values cannot be longer than their allowed lengths for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $this->createUser();
    $entity = ParDataEnforcementNotice::create($this->getEnforcementNoticeValues());
    $this->assertTrue($entity->save(), 'Par Enforcement Notice entity saved correctly.');
  }
}
