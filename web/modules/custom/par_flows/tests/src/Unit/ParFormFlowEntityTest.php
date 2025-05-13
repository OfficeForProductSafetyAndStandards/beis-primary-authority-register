<?php

namespace Drupal\Tests\par_flows\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\par_flows\Entity\ParFlow;

/**
 * Test the step lookup methods of the flow entity.
 *
 * @coversDefaultClass \Drupal\par_flows\Entity\ParFlow
 *
 * @group par_flows
 */
class ParFormFlowEntityTest extends UnitTestCase {

  /**
   * The test flow class to run the tests on.
   */
  protected $testFlow;

  /**
   * The current route for any given test.
   */
  protected string $currentRoute = 'par_test_forms.second';

  /**
   * The simulated previous step route for any given test.
   */
  protected string $previousRoute = 'par_test_forms.first';

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function setUp(): void {
    parent::setUp();

    $values = [
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
            'custom_step' => 1,
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
    ];
    $this->testFlow = $this->getMockBuilder(ParFlow::class)
      ->onlyMethods(['getCurrentRoute'])
      ->setConstructorArgs([$values, 'par_flow'])
      ->getMock();

    $this->testFlow
      ->expects($this->any())
      ->method('getCurrentRoute')
      ->will($this->returnCallback($this->getCurrentRoute(...)));

    // Check the entity is loaded with the default methods.
    $this->assertEquals('test', $this->testFlow->id());
  }

  /**
   *
   */
  public function getCurrentRoute(): string {
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
   * @covers ::progress
   */
  public function progress() {
    // Check previous step via custom redirect operation.
    $prev_url = $this->testFlow->progress('custom_step');

    // Check the flow progresses correctly for a given operation.
    $this->assertEquals('par_test_forms.first', $prev_url->getRouteName(), "The previous route has been correctly identified.");

    $prev_url = $this->testFlow->progress('cancel');

    // Check the flow progresses correctly for a given operation.
    $this->assertEquals('par_test_forms.confirmation', $prev_url->getRouteName(), "The previous route has been correctly identified given an operation.");

    // Check the default back operation is producing the expected result.
    $prev_url = $this->testFlow->progress(ParFlow::BACK_STEP);

    // Check the flow progresses correctly for a given operation.
    $this->assertEquals('par_test_forms.first', $prev_url->getRouteName(), "The previous route has been correctly identified.");

    $next_url = $this->testFlow->progress();

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.third', $next_url->getRouteName(), "The next route has been correctly identified.");

    $next_url = $this->testFlow->progress('save');

    // Check the next step is correct.
    $this->assertEquals('par_test_forms.fourth', $next_url->getRouteName(), "The next route has been correctly identified given an operation.");
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

    // Check the route and form_id.
    $this->assertEquals(4, count($form_ids), "All forms have been found.");

    $expected = [
      'par_test_first',
      'par_test_second',
      'par_test_third',
      'par_test_fourth',
    ];
    $this->assertEquals($expected, $form_ids, "The loaded forms are correct.");
  }

}
