<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataGeneralEnquiry;
use Drupal\par_data\Entity\ParDataGeneralEnquiryType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
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
    $values = [
      'request_date' => '',
      'notes' => '',
    ];

    $entity = ParDataGeneralEnquiry::create($values + $this->getGeneralEnquiryValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEquals(count($values), count($violations->getFieldNames()), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $entity = ParDataGeneralEnquiry::create($this->getGeneralEnquiryValues());
    $this->assertTrue($entity->save() === SAVED_NEW, 'Par General Enquiry entity saved correctly.');
  }
}
