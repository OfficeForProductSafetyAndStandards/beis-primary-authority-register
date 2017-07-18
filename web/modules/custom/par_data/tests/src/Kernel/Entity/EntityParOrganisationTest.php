<?php

namespace Drupal\Tests\par_data\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataOrganisationType;

/**
 * Tests PAR Organisation entity.
 *
 * @group PAR Data
 */
class EntityParOrganisationTest extends ParDataTestBase {

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testEntityValidate() {
    $this->createUser();

    $this->createUser();
    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $violations = $entity->validate();
    $this->assertEqual(count($violations), 0, 'No violations when validating a default PAR Organisation entity of type Business.');
  }

  /**
   * Test to validate a PAR Organisation entity.
   */
  public function testRequiredLengthFields() {
    $this->createUser();

    $entity = ParDataOrganisation::create([
      'type' => 'business',
      'name' => 'test',
      'uid' => 1,
      'organisation_name' => $this->randomString(501),
      'size' => $this->randomString(256),
      'employees_band' => $this->randomString(256),
      'nation' => $this->randomString(256),
      'comments' => $this->randomString(1000),
      'premises_mapped' => $this->randomString(10),
      'trading_name' => [
        $this->randomString(256),
      ],
    ] + $this->getOrganisationValues());
    $violations = $entity->validate()->getByFields([
      'organisation_name',
      'size',
      'employees_band',
      'nation',
      'comments',
      'premises_mapped',
      'trading_name',
    ]);
    $this->assertEqual(count($violations), 6, 'Field values cannot be longer than their allowed lengths.');
    $this->assertEqual($violations[0]->getMessage()->render(), t('%field: may not be longer than 500 characters.', ['%field' => 'Name']), 'The length of the Name field is correct..');
    $this->assertEqual($violations[1]->getMessage()->render(), t('%field: may not be longer than 255 characters.', ['%field' => 'Size']), 'The length of the Size field is correct.');
  }

  /**
   * Test to create and save a PAR Organisation entity.
   */
  public function testEntityCreate() {
    $this->createUser();

    $entity = ParDataOrganisation::create($this->getOrganisationValues());
    $this->assertTrue($entity->save(), 'PAR Organisation entity saved correctly.');
  }
}
