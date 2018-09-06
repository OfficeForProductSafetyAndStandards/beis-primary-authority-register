<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataInspectionFeedback;
use Drupal\par_data\Entity\ParDataInspectionFeedbackType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Inspection Feedback entity.
 *
 * @group PAR Data
 */
class EntityParInspectionFeedbackTest extends ParDataTestBase {

  /**
   * Test to validate an authority entity.
   */
  public function testEntityValidate() {
    $entity = ParDataInspectionFeedback::create($this->getInspectionFeedbackValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default Par Inspection Feedback entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testInspectionFeedbackFieldsExist() {
    $values = $this->getInspectionFeedbackValues();
    $entity = ParDataInspectionFeedback::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Inspection Feedback.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate an authority entity.
   */
  public function testInspectionFeedbackRequiredFields() {
    $values = [
      'request_date' => '',
      'notes' => '',
    ];

    $entity = ParDataInspectionFeedback::create($values + $this->getInspectionFeedbackValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $entity = ParDataInspectionFeedback::create($this->getInspectionFeedbackValues());
    $this->assertTrue($entity->save(), 'Par Inspection Feedback entity saved correctly.');
  }
}
