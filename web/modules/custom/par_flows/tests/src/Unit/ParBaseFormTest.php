<?php

namespace Drupal\Tests\par_flows\Unit;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\par_data\ParDataManagerInterface;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_flows\ParFlowDataHandlerInterface;
use Drupal\par_flows\ParFlowNegotiatorInterface;

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
   *
   * @var \Drupal\par_flows\Form\ParBaseForm
   */
  protected ParBaseForm $baseForm;

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    // Mock flow negotiator.
    $negotiator = $this->getMockBuilder(ParFlowNegotiatorInterface::class);

    // Mock data handler for flows.
    $data_handler = $this->createMock(ParFlowDataHandlerInterface::class);

    // Mock par data manager.
    $par_data_manager = $this->createMock(ParDataManagerInterface::class);

    // Mock entity repository.
    $component_plugin_manager = $this->createMock(PluginManagerInterface::class);

    $this->baseForm = $this->getMockBuilder(ParBaseForm::class)
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
