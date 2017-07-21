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
      ->setMethods(['getFlowName', 'getState', 'getIgnoredValues'])
      ->setConstructorArgs([$private_temp_store_factory, $session_manager_interface, $account_interface, $entity_repository])
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();

    $this->baseForm
      ->method('getFlowName')
      ->willReturn('test');
    $this->baseForm
      ->method('getState')
      ->willReturn('default');
    $this->baseForm
      ->method('getIgnoredValues')
      ->willReturn(['extra']);
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

  /**
   * @covers ::getFormKey
   * @covers ::normalizeKey
   */
  public function testGetFormKey() {
    $string = "random_string_" . $this->getRandomGenerator()->name(300);
    $key = $this->baseForm->getFormKey($string);
    $this->assertLessThan(255, $key, "The key length has been limited.");

    $starts_with = 'test_default_random_string_';
    $this->assertEquals($starts_with, substr($key, 0, strlen($starts_with)), "The key has been normalized.");
  }

  /**
   * @covers ::cleanseFormDefaults
   */
  public function testCleanseFormDefaults() {
    $form = [
      'form_build_id' => 'test',
      'form_token' => 'test',
      'form_id' => 'test',
      'op' => 'test',
      'extra' => 'test',
      'real_value' => 'test',
      'real_value2' => 'test',
    ];
    $expected = [
      'real_value' => 'test',
      'real_value2' => 'test',
    ];
    $this->assertArrayEquals($expected, $this->baseForm->cleanseFormDefaults($form), "The form values have been cleansed.");
  }

  /**
   * @covers ::decideBooleanValue
   */
  public function testdecideBooleanValue() {
    $on = "on_" . $this->getRandomGenerator()->name(20);
    $off = "off_" . $this->getRandomGenerator()->name(20);

    $this->assertEquals($on, $this->baseForm->decideBooleanValue($on, $on, $off), "The boolean value is identified as being 'on'.");
    $this->assertEquals($off, $this->baseForm->decideBooleanValue($off, $on, $off), "The boolean value is identified as being 'off'.");
    $this->assertEquals($off, $this->baseForm->decideBooleanValue($this->getRandomGenerator()->name(20), $on, $off), "The boolean value could not be detected and has been deemed as being 'off'.");
  }
}
