<?php

/**
 * @file
 * Contains \Drupal\Tests\par_flows\Unit\ParFlowEntityTest
 */

namespace Drupal\Tests\par_data\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\par_flows\Entity\ParFlow;

/**
 * Test the step lookup methods of the flow entity.
 *
 * @coversDefaultClass \Drupal\par_data\Entity\ParDataType
 *
 * @group par_data
 */
class ParDataTypeTest extends UnitTestCase {

  /**
   * The entity type class to run the tests on.
   */
  protected $parDataType;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $values = array(
      'id' => 'test',
      'title' => 'Test Data Entity Type',
      'description' => 'This is the test entity type.',
      'configuration' => [
        'entity' => [
          'status_field' => 'field_status',
          'label_field' => 'field_name',
          'required_fields' => [
            'field_1',
            'field_2',
            'field_3',
          ],
        ],
        'field_status' => [
          'allowed_values' => [
            'value_1' => 'First Value',
            'value_2' => 'Second Value',
            'value_3' => 'Third Value',
          ],
        ],
        'field_terms' => [
          'boolean_values' => [
            0 => ''
          ]
        ],
      ],
    );

    $this->parDataType = new ParFlow($values, 'par_flow');

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
   * @covers ::getStepByFormId
   */
  public function testStepByFormId() {
    $third_step = $this->testFlow->getStepByFormId('par_test_third');

    // Check the correct step was retrieved.
    $this->assertEquals(3, $third_step, "The step number has been retrieved by form id.");
  }

  /**
   * @covers ::getStepByRoute
   */
  public function testStepByRoute() {
    $fourth_step = $this->testFlow->getStepByRoute('par_test_forms.confirmation');

    // Check the correct step was retrieved.
    $this->assertEquals(4, $fourth_step, "The step number has been retrieved by form id.");
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
