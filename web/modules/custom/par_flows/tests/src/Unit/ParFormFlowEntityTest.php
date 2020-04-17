<?php

/**
 * @file
 * Contains \Drupal\Tests\par_flows\Unit\ParFlowEntityTest
 */

namespace Drupal\Tests\par_flows\Unit;

use Drupal\par_flows\ParFlowException;
use Drupal\Tests\UnitTestCase;
use Drupal\par_flows\Entity\ParFlow;

/**
 * Test the step lookup methods of the flow entity.
 *
 * @coversDefaultClass \Drupal\par_flows\Entity\ParFlow
 *
 * @group par_flows
 */
class ParFlowEntityTest extends UnitTestCase {

  /**
   * The test flow class to run the tests on.
   */
  protected $testFlow;

  /**
   * The current route for any given test.
   */
  protected $currentRoute = 'par_test_forms.second';

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
          'form_id' => 'par_test_first',
        ],
        2 => [
          'route' => 'par_test_forms.second',
          'form_id' => 'par_test_second',
          'redirect' => [
            'save' => 4,
            'cancel' => 5,
          ],
        ],
        3 => [
          'route' => 'par_test_forms.third',
          'form_id' => 'par_test_third',
          'redirect' => [
            'cancel' => 5,
          ],
        ],
        4 => [
          'route' => 'par_test_forms.fourth',
          'form_id' => 'par_test_fourth',
        ],
        5 => [
          'route' => 'par_test_forms.confirmation',
        ],
      ],
    );
    $this->testFlow = $this->getMockBuilder('Drupal\par_flows\Entity\ParFlow')
      ->setMethods(['getCurrentRoute'])
      ->setConstructorArgs([$values, 'par_flow'])
      ->getMock();

    $this->testFlow
      ->expects($this->any())
      ->method('getCurrentRoute')
      ->will($this->returnCallback([$this, 'getCurrentRoute']));

    // Check the entity is loaded with the default methods.
    $this->assertEquals('test', $this->testFlow->id());
  }

  public function getCurrentRoute() {
    return $this->currentRoute;
  }

  /**
   * @covers ::getSteps
   */
  public function testGetAllSteps() {
    $this->assertEquals(5, count($this->testFlow->getSteps()), "All steps have been loaded.");
  }

  /**
   * @covers ::getStep
   */
  public function testGetFirstStep() {
    $first_step = $this->testFlow->getStep(1);

    // Check the route and form_id.
    $this->assertEquals('par_test_forms.first', $first_step['route'], "The route has been retrieved for the first step.");
    $this->assertEquals('par_test_first', $first_step['form_id'], "The form id has been retrieved for the first step.");

    $non_existent_step = $this->testFlow->getStep(99);

    // Check NULL is returned for a non existant route.
    $this->assertNull($non_existent_step, "NULL is returned for a non-existent step.");
  }

  /**
   * @covers ::getCurrentStep
   */
  public function testCurrentStep() {
    $current_step = $this->testFlow->getCurrentStep();

    // Check the current step is correct.
    $this->assertEquals(2, $current_step, "The current step has been correctly identified.");

    // Check that an incorrect route returns NULL.
    $this->currentRoute = 'non_existant.route';
    $incorrect_current_step = $this->testFlow->getCurrentStep();

    $this->assertNull($incorrect_current_step, "The current step correctly returns NULL when not found.");
  }

  /**
   * @covers ::getNextStep
   */
  public function testGetNextStep() {
    $next_step = $this->testFlow->getNextStep();

    // Check the next step is correct.
    $this->assertEquals(3, $next_step, "The next step has been correctly identified.");

    // Check that the next step for a given operation is correct.
    $next_step_save = $this->testFlow->getNextStep('save');
    $next_step_cancel = $this->testFlow->getNextStep('cancel');

    $this->assertEquals(4, $next_step_save, "The save step has been correctly identified.");
    $this->assertEquals(5, $next_step_cancel, "The cancel step has been correctly identified.");

    // Check the next step for an incorrect operation.
    $next_step_fallback = $this->testFlow->getNextStep('non_existant_operation');

    $this->assertEquals(3, $next_step_fallback, "The flow goes to the next step if an incorrect operation is provided.");

    // Check the next step for an incorrect operation on the last step.
    $this->currentRoute = 'par_test_forms.confirmation';
    $next_step_last_fallback = $this->testFlow->getNextStep('non_existant_operation');

    $this->assertEquals(1, $next_step_last_fallback, "The flow goes back to the beginning if an incorrect operation is provided.");
  }

  /**
   * @covers ::getPrevStep
   */
  public function testGetPrevStep() {
    $next_step = $this->testFlow->getPrevStep();

    // Check the next step is correct.
    $this->assertEquals(1, $next_step, "The previous step has been correctly identified.");

    // Check that the next step for a given operation is correct.
    $prev_step_save = $this->testFlow->getPrevStep('save');
    $prev_step_cancel = $this->testFlow->getPrevStep('cancel');

    $this->assertEquals(4, $prev_step_save, "The save step has been correctly identified.");
    $this->assertEquals(5, $prev_step_cancel, "The cancel step has been correctly identified.");

    // Check the next step for an incorrect operation on the first step.
    $this->currentRoute = 'par_test_forms.first';
    $next_step_last_fallback = $this->testFlow->getPrevStep();

    $this->assertEquals(1, $next_step_last_fallback, "The flow stays at the first step.");
  }

  /**
   * @covers ::getNextStep
   * @covers ::getPrevStep
   *
   * @expectedException        \Drupal\par_flows\ParFlowException
   * @expectedExceptionMessage The specified route does not exist.
   */
  public function testInvalidSteps() {
    // Check the next step for an incorrect operation on the last step.
    $this->currentRoute = 'non_existant.route';
    $this->testFlow->getNextStep();

    // Check the next step for an incorrect operation on the last step.
    $this->currentRoute = 'non_existant.route';
    $this->testFlow->getPrevStep();
  }

  /**
   * @covers ::getNextRoute
   */
  public function testGetNextRoute() {
    $next_route = $this->testFlow->getNextRoute();

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.third', $next_route, "The next route has been correctly identified.");

    $next_route = $this->testFlow->progressRoute('save');

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.fourth', $next_route, "The next route has been correctly identified given an operation.");
  }

  /**
   * @covers ::getPrevRoute
   */
  public function testGetPrevRoute() {
    $prev_route = $this->testFlow->getPrevRoute();

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.first', $prev_route, "The previous route has been correctly identified.");

    $prev_route = $this->testFlow->getPrevRoute('cancel');

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.confirmation', $prev_route, "The previous route has been correctly identified given an operation.");
  }

  /**
   * @covers ::getStepByFormId
   */
  public function testStepByFormId() {
    $third_step = $this->testFlow->getStepByFormId('par_test_third');

    // Check the correct step was retrieved.
    $this->assertEquals(3, $third_step, "The step number has been retrieved by form id.");

    $non_existant_step = $this->testFlow->getStepByFormId('non_existant_form');

    $this->assertNull($non_existant_step, "NULL was correctly returned for a form that is not in the flow.");
  }

  /**
   * @covers ::getStepByRoute
   */
  public function testStepByRoute() {
    $fifth_step = $this->testFlow->getStepByRoute('par_test_forms.confirmation');

    // Check the correct step was retrieved.
    $this->assertEquals(5, $fifth_step, "The step number has been retrieved by form id.");

    $non_existant_step = $this->testFlow->getStepByFormId('non_existant.route');

    $this->assertNull($non_existant_step, "NULL was correctly returned for a route that is not in the flow.");
  }

  /**
   * @covers ::getRouteByStep
   */
  public function testGetRouteByStep() {
    $fourth_step = $this->testFlow->getRouteByStep(4);

    // Check the correct route was retrieved.
    $this->assertEquals('par_test_forms.fourth', $fourth_step, "The route was correctly retrieved by step number.");

    $non_existant_route = $this->testFlow->getRouteByStep(99);

    $this->assertNull($non_existant_route, "NULL route was correctly returned for a step that is not in the flow.");
  }

  /**
   * @covers ::getFormIdByStep
   */
  public function getFormIdByStep() {
    $fourth_step = $this->testFlow->getFormIdByStep(3);

    // Check the correct route was retrieved.
    $this->assertEquals('par_test_third', $fourth_step, "The route was correctly retrieved by step number.");

    $non_existant_form = $this->testFlow->getFormIdByStep(99);

    $this->assertNull($non_existant_form, "NULL route was correctly returned for a step that is not in the flow.");
  }

  /**
   * @covers ::getFlowForms
   */
  public function testGetFlowForms() {
    $form_ids = $this->testFlow->getFlowForms();

    // Check the route and form_id
    $this->assertEquals(4, count($form_ids), "All forms have been found.");

    $expected = [
      'par_test_first',
      'par_test_second',
      'par_test_third',
      'par_test_fourth',
    ];
    $this->assertArrayEquals($expected, $form_ids, "The loaded forms are correct.");
  }
}
