<?php

namespace Drupal\Tests\par_actions\Kernel\Entity;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data_test_entity\Entity\ParDataTestEntity;
use Drupal\Tests\par_actions\Kernel\ParSchedulerTestBase;

/**
 * Tests PAR Scheduler expiry.
 *
 * @group PAR Actions
 */
class SchedulerRuleExpiryTest extends ParSchedulerTestBase {

  /**
   * Test to validate a people entity.
   */
  public function testGetItems() {
    $entities = ['2019-01-01', '2020-01-01', '2021-01-01'];

    foreach ($entities as $expiry_date) {
      $entity = ParDataTestEntity::create(['expiry_date' => $expiry_date] + $this->getTestEntityValues());
      $entity->save();
    }

    // Check that scheduler get items return the correct number.
    $plugin = $this->getScheduleManager()->create('test_past');

    $entities = $plugin->getItems();
    var_dump(count($entities));

//    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }
}
