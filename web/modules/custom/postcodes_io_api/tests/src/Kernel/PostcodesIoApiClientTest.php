<?php

namespace Drupal\Tests\postcodes_io_api\Kernel;

use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\DependencyInjection\ContainerBuilder;

/**
 * Tests the PostcodesIoApiClient.
 *
 * @group postcodes_io_api
 */
class PostcodesIoApiClientTest extends KernelTestBase implements ServiceModifierInterface {

  protected $postcodesIoApiClient;

  /**
   * Outbound HTTP requests fail with KernelTestBase(TNG).
   *
   * See https://www.drupal.org/project/drupal/issues/2571475#comment-11938008.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   ContainerBuilder.
   */
  public function alter(ContainerBuilder $container) {
    $container->removeDefinition('test.http_client.middleware');
  }

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'postcodes_io_api',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['postcodes_io_api']);
    $this->postcodesIoApiClient = $this->container->get('postcodes_io_api.client');
  }

  /**
   * Test for postcode lookup success.
   */
  public function testLookup() {
    $result = $this->postcodesIoApiClient->lookup(
      [
        'postcode' => 'SW1A 0AA',
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for postcode bulk lookup success.
   */
  public function testBulkLookup() {
    $result = $this->postcodesIoApiClient->bulkLookup(
      [
        'postcodes' => [
          'SW1A 1AA',
          'SW1A 0AA',
        ],
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for reverse geocode lookup success.
   */
  public function testReverseGeocode() {
    $result = $this->postcodesIoApiClient->reverseGeocode(
      [
        'latitude' => 51.481667,
        'longitude' => -3.182155,
        'radius' => 1000,
        'limit' => 10,
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for bulk reverse geocode lookup success.
   */
  public function testBulkReverseGeocode() {
    $result = $this->postcodesIoApiClient->bulkReverseGeocode(
      [
        'geolocations' => [
          [
            'latitude' => 51.481667,
            'longitude' => -3.182155,
            'radius' => 100,
            'limit' => 5,
          ],
          [
            'latitude' => 51.88328,
            'longitude' => -3.43684,
            'radius' => 100,
            'limit' => 5,
          ],
        ],
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for matching postcode success.
   */
  public function testMatching() {
    $result = $this->postcodesIoApiClient->matching(
      [
        'query' => 'SW1A',
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for postcode validate success.
   */
  public function testValidate() {
    $result = $this->postcodesIoApiClient->validate(
      [
        'postcode' => 'SW1A 0AA',
      ],
      FALSE
    );

    $this->assertEquals(TRUE, $result);
  }

  /**
   * Test for autocomplete postcode success.
   */
  public function testAutocomplete() {
    $result = $this->postcodesIoApiClient->autocomplete(
      [
        'postcode' => 'SW1A',
        'limit' => 10,
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for random postcode success.
   */
  public function testRandom() {
    $result = $this->postcodesIoApiClient->random(FALSE);
    $this->assertEquals(TRUE, is_array($result));
  }

  /**
   * Test for Outward Code Lookup success.
   */
  public function testOutwardCodeLookup() {
    $result = $this->postcodesIoApiClient->outwardCodeLookup(
      [
        'outcode' => 'SW1A',
      ],
      FALSE
    );

    $this->assertEquals(TRUE, is_array($result));
  }

}
