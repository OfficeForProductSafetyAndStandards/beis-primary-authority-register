<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Deviation Request entity.
 *
 * @group PAR Data
 */
class EntityParGeneralEnquiryTest extends ParDataTestBase {

  /**
   * Test to validate a deviation request entity.
   */
  public function testEntityValidate() {
    $entity = ParDataGeneralEnquiry::create($this->getGeneralEnquiryValues());
    $violations = $entity->validate();
    $this->assertEquals(0, count($violations->getFieldNames()), 'No violations when validating a default Par General Enquiry entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testGeneralEnquiryFieldsExist() {
    $values = $this->getGeneralEnquiryValues();
    $entity = ParDataGeneralEnquiry::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for General Enquiry.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate an authority entity.
   */
  public function testGeneralEnquiryRequiredFields() {
    // List of fields that have the addConstraint() applied.
    $values = [
      // @see ParDataEntity::baseFieldDefinitions()
      'archive_reason' => '',
      // @see ParDataGeneralEnquiry::baseFieldDefinitions()
      'request_date' => '',
      'notes' => '',
      'primary_authority_notes' => '',
    ];

    $entity = ParDataGeneralEnquiry::create($values + $this->getGeneralEnquiryValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEquals(
      count($values),
      count($violations->getFieldNames()),
      t(
        'Violations are reported for fields @fields.',
        ['@fields' => implode(', ', $violations->getFieldNames())]
      )->render()
    );
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $entity = ParDataGeneralEnquiry::create($this->getGeneralEnquiryValues());
    $this->assertTrue($entity->save() === SAVED_NEW, 'Par General Enquiry entity saved correctly.');
  }

}
