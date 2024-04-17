<?php

namespace Drupal\Tests\par_flows\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Test the logical methods of the base form.
 *
 * @coversDefaultClass \Drupal\par_flows\Form\ParBaseForm
 *
 * @group par_flows
 */
class ParBaseFormTest extends UnitTestCase {

  /**
   * The test flow class to run the tests on.
   */
  protected $baseForm;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Mock flow negotiator.
    $negotiator = $this->getMockBuilder('Drupal\par_flows\ParFlowNegotiatorInterface');

    // Mock data handler for flows.
    $data_handler = $this->createMock('Drupal\par_flows\ParFlowDataHandlerInterface');

    // Mock par data manager.
    $par_data_manager = $this->createMock('Drupal\par_data\ParDataManagerInterface');

    // Mock entity repository.
    $component_plugin_manager = $this->createMock('Drupal\Component\Plugin\PluginManagerInterface');

    $this->baseForm = $this->getMockBuilder('Drupal\par_flows\Form\ParBaseForm')
      ->onlyMethods(['getIgnoredValues'])
      ->setConstructorArgs([$negotiator, $data_handler, $par_data_manager, $component_plugin_manager])
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();

    $this->baseForm->expects($this->any())
      ->method('getIgnoredValues')
      ->willReturn(['extra']);
  }

  /**
   * @covers ::decideBooleanValue
   */
  public function testdecideBooleanValue() {
    $on = "on_" . $this->getRandomGenerator()->name(20);
    $off = "off_" . $this->getRandomGenerator()->name(20);

    $this->assertTrue($this->baseForm->decideBooleanValue($on, $on, $off), "The boolean value is identified as being 'on'.");
    $this->assertFalse($this->baseForm->decideBooleanValue($off, $on, $off), "The boolean value is identified as being 'off'.");

    $this->assertFalse($this->baseForm->decideBooleanValue(1), "The boolean value is correctly identified as being 'off'.");
    $this->assertFalse($this->baseForm->decideBooleanValue('1'), "The boolean value is correctly identified as being 'off'.");
    $this->assertTrue($this->baseForm->decideBooleanValue('on'), "The boolean value is correctly identified as being 'on'.");
    $this->assertTrue($this->baseForm->decideBooleanValue(TRUE), "The boolean value is correctly identified as being 'on'.");
  }

}
