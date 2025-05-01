<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\Tests\par_data\Kernel\ParDataTestBase;

/**
 * Tests PAR Deviation Request entity.
 *
 * @group PAR Data
 */
class EntityParDeviationRequestTest extends ParDataTestBase {

  /**
   * Test to validate a deviation request entity.
   */
  public function testEntityValidate() {
    $entity = ParDataDeviationRequest::create($this->getDeviationRequestValues());
    /** @var \Drupal\Core\Entity\EntityConstraintViolationListInterface $violations */
    $violations = $entity->validate();
    $this->assertEquals(0, count($violations->getFieldNames()), 'No violations when validating a default Par Deviation Request entity.');
  }

  /**
   * Test all authority fields exist.
   */
  public function testDeviationRequestFieldsExist() {
    $values = $this->getDeviationRequestValues();
    $entity = ParDataDeviationRequest::create($values);

    foreach (array_diff_key($values, $this->getBaseValues()) as $field => $value) {
      $this->assertTrue($entity->hasField($field), t('Field %field exists for Deviation Request.', ['%field' => $field]));
    }
  }

  /**
   * Test to validate required fields.
   */
  public function testDeviationRequestRequiredFields() {
    // List of fields that have the addConstraint() applied.
    $values = [
      // @see ParDataEntity::baseFieldDefinitions()
      'archive_reason' => '',
      // @see ParDataDeviationRequest::baseFieldDefinitions()
      'request_date' => '',
      'notes' => '',
      'document' => '',
    ];

    $entity = ParDataDeviationRequest::create($values + $this->getDeviationRequestValues());
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
    $entity = ParDataDeviationRequest::create($this->getDeviationRequestValues());
    $this->assertTrue($entity->save() === SAVED_NEW, 'Par Deviation Request entity saved correctly.');
  }

}
