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
   * Test to validate a PAR Partnership entity.
   */
  public function testEntityValidate() {
    $this->createUser();
    $entity = ParDataPartnership::create($this->getPartnershipValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Partnership entity.');
  }

  /**
   * Test to validate a PAR Partnership entity.
   */
  public function testRequiredFields() {
    $this->createUser();

    $entity = ParDataPartnership::create([
      'type' => 'partnership',
      'name' => 'test',
      'uid' => 1,
      'partnership_type' => '',
      'partnership_status' => '',
    ]);
    $violations = $entity->validate()->getByFields([
      'partnership_type',
      'partnership_status',
      'about_partnership',
      'communication_email',
      'communication_phone',
      'communication_notes',
      'approved_date',
      'expertise_details',
      'cost_recovery',
      'reject_comment',
      'revocation_source',
      'revocation_date',
      'revocation_reason',
      'authority_change_comment',
      'organisation_change_comment',
    ]);
    $this->assertEqual(count($violations), 2, 'Required fields cannot be empty.');
    $this->assertEqual($violations[0]->getMessage()->render(), 'This value should not be null.', 'These fields are required.');
  }

  /**
   * Test to validate a PAR Partnership entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    $entity = ParDataPartnership::create([
      'type' => 'partnership',
      'name' => 'test',
      'uid' => 1,
      'partnership_type' => $this->randomString(256),
      'partnership_status' => $this->randomString(256),
      'about_partnership' => $this->randomString(1000),
      'communication_email' => $this->randomString(10),
      'communication_phone' => $this->randomString(10),
      'communication_notes' => $this->randomString(1000),
      'approved_date' => $this->randomString(10),
      'expertise_details' => $this->randomString(1000),
      'cost_recovery' => $this->randomString(256),
      'reject_comment' => $this->randomString(1000),
      'revocation_source' => $this->randomString(256),
      'revocation_date' => $this->randomString(10),
      'revocation_reason' => $this->randomString(1000),
      'authority_change_comment' => $this->randomString(1000),
      'organisation_change_comment' => $this->randomString(1000),
    ]);
    $violations = $entity->validate()->getByFields([
      'partnership_type',
      'partnership_status',
      'about_partnership',
      'communication_email',
      'communication_phone',
      'communication_notes',
      'approved_date',
      'expertise_details',
      'cost_recovery',
      'reject_comment',
      'revocation_source',
      'revocation_date',
      'revocation_reason',
      'authority_change_comment',
      'organisation_change_comment',
    ]);
    $this->assertEqual(count($violations), 8, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[0]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Partnership Type']), 'The length of the Partnership Type field is correct..');
    $this->assertEqual($violations[3]->getMessage()->render(), 'This value should be of the correct primitive type.', 'The input type of the Communication by Phone field is correct.');
    $this->assertEqual($violations[4]->getMessage()->render(), 'This value should be of the correct primitive type.', 'The input type of the Approved Date field is correct.');
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
