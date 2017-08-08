<?php

/**
 * @file
 * Contains \Drupal\Tests\par_flows\Unit\ParFlowEntityTest
 */

namespace Drupal\Tests\par_data\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Test the step lookup methods of the flow entity.
 *
 * @coversDefaultClass \Drupal\par_data\Entity\ParDataType
 *
 * @group par_data_base
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

    $configuration = [
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
          0 => 'Agreed',
          1 => 'Awaiting confirmation',
        ],
      ],
      'field_salutation' => [
        'allowed_values' => [
          'mrs' => 'Mrs',
          'mr' => 'Mr',
          'ms' => 'Ms',
          'dr' => 'Dr',
        ],
      ],
    ];

    $this->parDataType = $this->getMockForAbstractClass('\Drupal\par_data\Entity\ParDataType');
    $this->parDataType->expects($this->any())
      ->method('getConfiguration')
      ->will($this->returnValue($configuration));
  }

  /**
   * @covers ::getConfigurationElement
   */
  public function testElementConfiguration() {
    $expected = [
      'allowed_values' => [
        'mrs' => 'Mrs',
        'mr' => 'Mr',
        'ms' => 'Ms',
        'dr' => 'Dr',
      ],
    ];
    $actual = $this->parDataType->getConfigurationElement('field_salutation');

    $this->assertArrayEquals($expected, $actual, "All steps have been loaded.");
  }
}
