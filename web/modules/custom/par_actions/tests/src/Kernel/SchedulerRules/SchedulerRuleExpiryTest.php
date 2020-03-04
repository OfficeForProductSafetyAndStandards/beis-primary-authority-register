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
   *
   * Jan 2020
   * | Mon | Tue | Wed | Thur | Fri | Sat* | Sun* |
   * |-----|-----|-----|------|-----|------|------|
   * |   - |   - |   1 |    2 |   3 |    4 |    5 |
   * |   6 |   7 |   8 |    9 |  10 |   11 |   12 |
   * |  13 |  14 |  15 |   16 |  17 |   18 |   19 |
   * |  20 |  21 |  22 |   23 |  24 |   25 |   26 |
   * |  27 |  28 |  29 |   30 |  31 |      |      |
   *
   * Feb 2020
   * | Mon | Tue | Wed | Thur | Fri | Sat* | Sun* |
   * |-----|-----|-----|------|-----|------|------|
   * |   - |   - |   - |    - |   - |    1 |    2 |
   * |   3 |   4 |   5 |    6 |   7 |    8 |    9 |
   * |  10 |  11 |  12 |   13 |  14 |   15 |   16 |
   * |  17 |  18 |  19 |   20 |  21 |   22 |   23 |
   * |  24 |  25 |  26 |   27 |  28 |   29 |      |
   */
  public function testGetItems() {
    // Create an entity to expire on every Friday and Monday in January.
    $entities = ['2019-01-03', '2020-01-06', '2021-01-10', '2021-01-13', '2021-01-17', '2021-01-20', '2021-01-24', '2021-01-27', '2021-01-31'];

    foreach ($entities as $expiry_date) {
      $entity = ParDataTestEntity::create(['expiry_date' => $expiry_date] + $this->getTestEntityValues());
      $entity->save();
    }

    // Check that scheduler get items return the correct number.
    $plugins = $this->schedulerManager->getDefinitions();
    $plugin = $this->schedulerManager->createInstance('test_past', $plugins['test_past']);

    // Set the current time for the plugin to test.
    $plugin->setCurrentTime('2021-01-20');

    $entities = $plugin->getItems();
    var_dump(count($entities));

//    $this->assertEqual(count($violations->getFieldNames()), count($values), t('Field values are required for %fields.', ['%fields' => implode(', ', $violations->getFieldNames())]));
  }
}
