<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataDeviationRequest;
use Drupal\par_data\Entity\ParDataDeviationRequestType;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPerson;
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
    $violations = $entity->validate();
    $this->assertEqual(count($violations->getFieldNames()), 0, 'No violations when validating a default Par Deviation Request entity.');
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
    $values = [
      'request_date' => '',
      'notes' => '',
    ];

    $entity = ParDataDeviationRequest::create($values + $this->getDeviationRequestValues());
    $violations = $entity->validate()->getByFields(array_keys($values));
    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }

  /**
   * Test to create and save an authority entity.
   */
  public function testEntityCreate() {
    $entity = ParDataDeviationRequest::create($this->getDeviationRequestValues());
    $this->assertTrue($entity->save(), 'Par Deviation Request entity saved correctly.');
  }
}
