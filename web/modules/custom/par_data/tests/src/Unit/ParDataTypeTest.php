<?php

/**
 * @file
 * Contains \Drupal\Tests\par_flows\Unit\ParFlowEntityTest
 */

namespace Drupal\Tests\par_data\Unit;

use Drupal\par_data\Entity\ParDataPartnershipType;
use Drupal\Tests\UnitTestCase;
use Drupal\par_data\Entity\ParDataType;

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
            0 => 'Agreed',
            1 => 'Awaiting confirmation',
          ]
        ],
        'field_salutation' => [
          'allowed_values' => [
            'mrs' => 'Mrs',
            'mr' => 'Mr',
            'ms' => 'Ms',
            'dr' => 'Dr',
          ],
        ],
      ],
    );

    $this->parDataType = $this->getMockForAbstractClass('Drupal\par_data\Entity\ParDataType', [$values, 'test']);
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

    $this->assertArrayEquals($expected, $actual, "The configuration element for the field_salutation field has been returned.");
  }

  /**
   * @covers ::getConfigurationElement
   */
  public function testElementConfigurationEmpty() {
    $actual = $this->parDataType->getConfigurationElement('field_unknown');
    $this->assertArrayEquals([], $actual, "Unknown field configuration not found.");
  }

  /**
   * @covers ::getConfigurationByType
   */
  public function testTypeConfiguration() {
    $expected = [
      'field_status' => [
        'value_1' => 'First Value',
        'value_2' => 'Second Value',
        'value_3' => 'Third Value',
      ],
      'field_salutation' => [
        'mrs' => 'Mrs',
        'mr' => 'Mr',
        'ms' => 'Ms',
        'dr' => 'Dr',
      ],
    ];
    $actual = $this->parDataType->getConfigurationByType('allowed_values');

    $this->assertArrayEquals($expected, $actual, "Allowed values have been returned.");
  }

  /**
   * @covers ::getConfigurationByType
   */
  public function testTypeConfigurationEmpty() {
    $actual = $this->parDataType->getConfigurationByType('unknown_configuration');
    $this->assertArrayEquals([], $actual, "Unknown configuration not found.");
  }

  /**
   * @covers ::getConfigurationElementByType
   */
  public function testElementTypeConfiguration() {
    $expected = [
      0 => 'Agreed',
      1 => 'Awaiting confirmation',
    ];
    $actual = $this->parDataType->getConfigurationElementByType('field_terms', 'boolean_values');

    $this->assertArrayEquals($expected, $actual, "Boolean values for the field_terms field have been returned.");

    $label_field = $this->parDataType->getConfigurationElementByType('entity', 'label_field');
    $this->assertEquals('field_name', $label_field, "The label field is correctly configured.");
  }



  /**
   * @covers ::getConfigurationElementByType
   */
  public function testElementTypeConfigurationEmpty() {
    $actual = $this->parDataType->getConfigurationElementByType('field_unknown', 'boolean_values');
    $this->assertNull($actual, "Boolean values not returned for unknown field.");

    $actual = $this->parDataType->getConfigurationElementByType('field_terms', 'unknown_configuration');
    $this->assertNull($actual, "Unknown value not returned for field_terms field.");
  }
}
