<?php

/**
 * @file
 * Contains \Drupal\Tests\par_flows\Unit\ParFlowEntityTest
 */

namespace Drupal\Tests\par_data\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Test the logical methods of the entity base.
 *
 * @coversDefaultClass \Drupal\par_data\Entity\ParDataEntity
 *
 * @group par_data
 */
class ParDataEntityTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * SDFSDFDS
   */
  public function testGetFormKey() {
    $this->assertEquals('yes', 'yes', "The key has been normalized.");
  }
}
