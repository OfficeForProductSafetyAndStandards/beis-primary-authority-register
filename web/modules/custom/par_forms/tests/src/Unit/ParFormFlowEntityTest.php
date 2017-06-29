<?php

/**
 * @file
 * Contains \Drupal\Tests\par_forms\Unit\ParFormFlowEntityTest
 */

namespace Drupal\Tests\par_forms\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\par_forms\Entity\ParFormFlow;

/**
 * Test the step lookup methods of the flow entity.
 *
 * @coversDefaultClass \Drupal\par_forms\Entity\ParFormFlow
 *
 * @group par_forms
 */
class ParFormFlowEntityTest extends UnitTestCase {

  /**
   * The test flow class to run the tests on.
   */
  protected $testFlow;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $values = array(
      'id' => 'test',
      'title' => 'Test Form Flow',
      'description' => 'This is the test form flow, it is very similar to the example.',
      'steps' => [
        1 => [
          'route' => 'par_test_forms.first',
          'form_id' => 'par_test_first'
        ],
        2 => [
          'route' => 'par_test_forms.second',
          'form_id' => 'par_test_second'
        ],
        3 => [
          'route' => 'par_test_forms.third',
          'form_id' => 'par_test_third'
        ],
        4 => [
          'route' => 'par_test_forms.confirmation',
        ],
      ],
    );
    $this->testFlow = new ParFormFlow($values, 'par_form_flow');

    // Check the entity is loaded with the default methods.
    $this->assertEquals('test', $this->testFlow->id());
  }

  /**
   * @covers ::getSteps
   */
  public function testGetAllSteps() {
    $this->assertEquals(4, count($this->testFlow->getSteps()), "All steps have been loaded.");
  }

  /**
   * @covers ::getStep
   */
  public function testGetFirstStep() {
    $first_step = $this->testFlow->getStep(1);

    // Check the route and form_id
    $this->assertEquals('par_test_forms.first', $first_step['route'], "The route has been retrieved for the first step.");
    $this->assertEquals('par_test_first', $first_step['form_id'], "The form id has been retrieved for the first step.");
  }

  /**
   * @covers ::getStep
   */
  public function testNonExistentStep() {
    $non_existent_step = $this->testFlow->getStep(99);

    // Check the route and form_id
    $this->assertNull($non_existent_step, "Null is returned for a non-existent step.");
  }

  /**
   * @covers ::getStep
   */
  public function testStepByFormId() {
    $third_step = $this->testFlow->getStepByFormId('par_test_third');

    // Check the route and form_id
    $this->assertEquals(3, $third_step['step'], "The step number has been retrieved by form id.");
    $this->assertEquals('par_test_forms.third', $third_step['route'], "The route has been retrieved by form id.");
    $this->assertEquals('par_test_third', $third_step['form_id'], "The form id has been retrieved by form id.");
  }

  /**
   * @covers ::getStep
   */
  public function testGetFlowForms() {
    $form_ids = $this->testFlow->getFlowForms();

    // Check the route and form_id
    $this->assertEquals(3, count($form_ids), "All forms have been found.");

    $expected = [
      'par_test_first',
      'par_test_second',
      'par_test_third',
    ];
    $this->assertArrayEquals($expected, $form_ids, "The loaded forms are correct.");
  }
}
