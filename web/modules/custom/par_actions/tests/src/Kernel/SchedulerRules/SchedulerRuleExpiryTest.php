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
   * Test to present entity events.
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
  public function testCurrentItems() {
    // Create an entity to expire on every Friday and Monday in January.
    $entities = ['2020-01-03', '2020-01-06', '2020-01-10', '2020-01-13', '2020-01-17', '2020-01-20', '2020-01-24', '2020-01-27', '2020-01-31'];

    foreach ($entities as $expiry_date) {
      $entity = ParDataTestEntity::create(['expiry_date' => $expiry_date] + $this->getTestEntityValues());
      $entity->save();
    }

    // Check that scheduler get items return the correct number.
    $plugins = $this->schedulerManager->getDefinitions();
    $plugin = $this->schedulerManager->createInstance('test_current', $plugins['test_current']);

    // Set the current time for the plugin to test.
    $plugin->setCurrentTime('20-01-2020');

    $entities = $plugin->getItems();
    $assertions = ['2020-01-03', '2020-01-06', '2020-01-10', '2020-01-13', '2020-01-17', '2020-01-20'];
    foreach ($entities as $entity){
      if (!in_array($entity->get('expiry_date')->getString(), $assertions)) {
        $this->assertFalse(TRUE, t('Retrieved an incorrectly expired entity with expiry date: %date.', ['%date' => (string) $entity->get('expiry_date')->getString()]));
      }
    }

    $this->assertEqual(count($entities), count($assertions), t('Retrieved %assertions expired entities.', ['%assertions' => (string) count($assertions)]));
  }

  /**
   * Test to prior entity events.
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
  public function testBeforeItems() {
    // Create an entity to expire on every Friday and Monday in January.
    $entities = ['2020-01-03', '2020-01-06', '2020-01-10', '2020-01-13', '2020-01-17', '2020-01-20', '2020-01-24', '2020-01-27', '2020-01-31'];

    foreach ($entities as $expiry_date) {
      $entity = ParDataTestEntity::create(['expiry_date' => $expiry_date] + $this->getTestEntityValues());
      $entity->save();
    }

    // Check that scheduler get items return the correct number.
    $plugins = $this->schedulerManager->getDefinitions();
    $plugin = $this->schedulerManager->createInstance('test_before', $plugins['test_before']);

    // Set the current time for the plugin to test.
    $plugin->setCurrentTime('20-01-2020');

    $entities = $plugin->getItems();
    $assertions = ['2020-01-03', '2020-01-06', '2020-01-10', '2020-01-13', '2020-01-17', '2020-01-20', '2020-01-24', '2020-01-27'];
    foreach ($entities as $entity){
      if (!in_array($entity->get('expiry_date')->getString(), $assertions)) {
        $this->assertFalse(TRUE, t('Retrieved an incorrectly expired entity with expiry date: %date.', ['%date' => (string) $entity->get('expiry_date')->getString()]));
      }
    }

    $this->assertEqual(count($entities), count($assertions), t('Retrieved %assertions expired entities.', ['%assertions' => (string) count($assertions)]));
  }

  /**
   * Test to post entity events.
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
  public function testAfterItems() {
    // Create an entity to expire on every Friday and Monday in January.
    $entities = ['2020-01-03', '2020-01-06', '2020-01-10', '2020-01-13', '2020-01-17', '2020-01-20', '2020-01-24', '2020-01-27', '2020-01-31'];

    foreach ($entities as $expiry_date) {
      $entity = ParDataTestEntity::create(['expiry_date' => $expiry_date] + $this->getTestEntityValues());
      $entity->save();
    }

    // Check that scheduler get items return the correct number.
    $plugins = $this->schedulerManager->getDefinitions();
    $plugin = $this->schedulerManager->createInstance('test_after', $plugins['test_after']);

    // Set the current time for the plugin to test.
    $plugin->setCurrentTime('20-01-2020');

    $entities = $plugin->getItems();
    $assertions = ['2020-01-03', '2020-01-06', '2020-01-10'];
    foreach ($entities as $entity){
      if (!in_array($entity->get('expiry_date')->getString(), $assertions)) {
        $this->assertFalse(TRUE, t('Retrieved an incorrectly expired entity with expiry date: %date.', ['%date' => (string) $entity->get('expiry_date')->getString()]));
      }
    }

    $this->assertEqual(count($entities), count($assertions), t('Retrieved %assertions expired entities.', ['%assertions' => (string) count($assertions)]));
  }
}
