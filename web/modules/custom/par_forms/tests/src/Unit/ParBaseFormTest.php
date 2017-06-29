<?php

/**
 * @file
 * Contains \Drupal\Tests\par_forms\Unit\ParFormFlowEntityTest
 */

namespace Drupal\Tests\par_forms\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\par_forms\Form\ParBaseForm;

/**
 * Test the logical methods of the base form.
 *
 * @coversDefaultClass \Drupal\par_forms\Form\ParBaseForm
 *
 * @group par_forms
 */
class ParBaseFormTest extends UnitTestCase {

  /**
   * The test flow class to run the tests on.
   */
  protected $baseForm;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // PrivateTempStoreFactory
    // SessionManagerInterface
    // AccountInterface
    // EntityStorageInterface

    // Mock private temp store.
    $private_temp_store_factory = $this->getMockBuilder('Drupal\user\PrivateTempStoreFactory')
      ->disableOriginalConstructor()
      ->getMock();

    // Mock entity type manager.
    $session_manager_interface = $this->getMock('Drupal\Core\Session\SessionManagerInterface');

    // Mock logger factory.
    $account_interface = $this->getMock('Drupal\Core\Session\AccountInterface');

    // Mock entity repository.
    $entity_repository = $this->getMock('Drupal\Core\Entity\EntityStorageInterface');

    $this->baseForm = $this->getMockBuilder('Drupal\par_forms\Form\ParBaseForm')
      ->setConstructorArgs([$private_temp_store_factory, $session_manager_interface, $account_interface, $entity_repository])
      ->getMockForAbstractClass();

    $this->baseForm
      ->expects($this->any())
      ->method('getChildren')
      ->willReturn($this->returnCallback([static::class, 'getChildren']));
  }

  function getChildren($elements) {
    $children = [];

    foreach ($elements as $key => $value) {
      if (!empty($value) && is_array($value)) {
        $children[$key] = $value;
      }
    }

    return array_keys($children);
  }
}
