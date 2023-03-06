<?php

namespace Drupal\postcodes_io_api\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Postcodes IO API Client.
 *
 * @package Drupal\postcodes_io_api\Service
 */
class PostcodesIoApiClient {

  /**
   * Settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $settings;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Cache Backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Client Factory.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  protected $httpClientFactory;

  /**
   * Guzzle Service Client.
   *
   * @var \GuzzleHttp\Command\Guzzle\GuzzleClient
   */
  protected $guzzleClient;

  /**
   * Module Handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * Client constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $httpClientFactory
   *   A Guzzle client object.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   Config.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module Handler.
   */
  public function __construct(ClientFactory $httpClientFactory,
                              ConfigFactory $config,
                              CacheBackendInterface $cacheBackend,
                              LoggerChannelFactoryInterface $loggerFactory,
                              ModuleHandlerInterface $moduleHandler) {
    $this->settings = $config->get('postcodes_io_api.settings');

    $this->cacheBackend = $cacheBackend;
    $this->loggerFactory = $loggerFactory;
    $this->httpClientFactory = $httpClientFactory;
    $this->moduleHandler = $moduleHandler;
    $this->guzzleClient = $this->getGuzzleClient();
  }

  /**
   * Postcodes.io request.
   *
   * @param string $op
   *   Method to call.
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return bool|array
   *   Either an array or false.
   */
  private function request($op, array $args, $cacheable = TRUE) {
    $cid = $this->buildCacheId($op, $args);

    // Check cache.
    $cachedResponse = $this->cacheGet($cid, $cacheable);
    if ($cachedResponse) {
      return $cachedResponse;
    }

    // No cache. Do it the hard way.
    $response = $this->doRequest($op, $args);

    if ($response) {
      // Set cache.
      $this->cacheSet($cid, $response, $cacheable);

      // Return result from source if found.
      return $response;
    }

    // Tough luck, no results mate.
    return FALSE;
  }

  /**
   * Build Hash from Args array.
   *
   * @param string $op
   *   Method to call.
   * @param array $args
   *   Args to request.
   *
   * @return string
   *   Return string.
   */
  private function buildCacheId($op, array $args) {
    // Sort the args for consistency.
    ksort($args);

    return 'postcodes_io_api:' . md5($op . serialize($args));
  }

  /**
   * Guzzle request for Postcodes.io.
   *
   * @param string $op
   *   Operation.
   * @param array $parameters
   *   Parameters.
   *
   * @return bool|array
   *   False or array.
   */
  private function doRequest($op, array $parameters = []) {
    try {
      $response = $this->guzzleClient->$op($parameters);
      return $response['result'];
    }
    catch (GuzzleException $e) {
      ksm($e);
      $this->loggerFactory->get('postcodes_io_api')->error("@message", ['@message' => $e->getMessage()]);
      return NULL;
    }
  }

  /**
   * Set results from cache.
   *
   * @param string $cid
   *   Cache ID.
   * @param bool $cacheable
   *   Is it cacheable?
   *
   * @return array|bool|null
   *   Result or null
   */
  private function cacheGet($cid, $cacheable) {
    // Are we allowed to use cached result for this?
    if ($cacheable == TRUE) {
      $apiCacheMaximumAge = $this->settings->get('api_cache_maximum_age');
      if ($apiCacheMaximumAge != 0) {
        // Check cache.
        if ($cache = $this->cacheBackend->get($cid)) {
          $response = $cache->data;

          // Return result from cache if found.
          return $response;
        }
      }
    }

    return NULL;
  }

  /**
   * Set cache.
   *
   * @param string $cid
   *   Cache ID.
   * @param array|bool $response
   *   Response.
   * @param bool $cacheable
   *   Is it cacheable?
   */
  private function cacheSet($cid, $response, $cacheable) {
    if ($cacheable == TRUE) {
      // Cache the response if we got one.
      $apiCacheMaximumAge = $this->settings->get('api_cache_maximum_age');
      if ($apiCacheMaximumAge != 0) {
        $this->cacheBackend->set($cid, $response, time() + $apiCacheMaximumAge);
      }
    }
  }

  /**
   * Get GuzzleClient.
   *
   * @return \GuzzleHttp\Command\Guzzle\GuzzleClient
   *   GuzzleClient.
   */
  private function getGuzzleClient() {
    // Get a Guzzle Description from YAML.
    $apiDescription = $this->getDescription();

    // We will use guzzle services client from here on.
    return new GuzzleClient($this->httpClientFactory->fromOptions(), $apiDescription);
  }

  /**
   * Get guzzle description.
   */
  private function getDescription() {
    // Load descriptions.
    $modulePath = $this->moduleHandler->getModule('postcodes_io_api')->getPath();
    $filePath = $modulePath . '/src/api/postcodes_io_api.description.yml';
    $fileContents = file_get_contents($filePath);
    $description = Yaml::parse($fileContents);

    // Override the base url from the one set in settings.
    $settings = $this->settings->get();
    $description['baseUrl'] = $settings['base_url'];

    $apiDescription = new Description($description);
    return $apiDescription;
  }

  /**
   * Postcode Lookup.
   *
   * This uniquely identifies a postcode.
   * Returns a single postcode entity for a given
   * postcode (case, space insensitive).
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function lookup(array $args, $cacheable = TRUE) {
    return $this->request('lookup', $args, $cacheable);
  }

  /**
   * Bulk Postcode Lookup.
   *
   * Accepts an array of postcodes.
   * Returns a list of matching postcodes and respective available data.
   * Accepts up to 100 postcodes.
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function bulkLookup(array $args, $cacheable = TRUE) {
    return $this->request('bulkLookup', $args, $cacheable);
  }

  /**
   * Reverse Geocoding.
   *
   * Returns nearest postcodes for a given longitude and latitude.
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function reverseGeocode(array $args, $cacheable = TRUE) {
    return $this->request('reverseGeocode', $args, $cacheable);
  }

  /**
   * Bulk Reverse Geocoding.
   *
   * Bulk translates geolocations into Postcodes.
   * Accepts up to 100 geolocations.
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function bulkReverseGeocode(array $args, $cacheable = TRUE) {
    return $this->request('bulkReverseGeocode', $args, $cacheable);
  }

  /**
   * Postcode Query.
   *
   * Submit a postcode query and receive a complete list of postcode
   * matches and all associated postcode data.
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function matching(array $args, $cacheable = TRUE) {
    return $this->request('matching', $args, $cacheable);
  }

  /**
   * Postcode Validation.
   *
   * Method to validate a postcode.
   * Returns true or false (meaning valid or invalid respectively).
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function validate(array $args, $cacheable = TRUE) {
    return $this->request('validate', $args, $cacheable);
  }

  /**
   * Postcode Autocomplete.
   *
   * Convenience method to return an list of matching postcodes.
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function autocomplete(array $args, $cacheable = TRUE) {
    return $this->request('autocomplete', $args, $cacheable);
  }

  /**
   * Random Postcode.
   *
   * Returns a random postcode and all available data for that postcode.
   *
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function random($cacheable = TRUE) {
    return $this->request('random', [], $cacheable);
  }

  /**
   * Outward Code Lookup.
   *
   * Geolocation data for the centroid of the outward code specified.
   * The outward code represents the first half of any postcode
   * (separated by a space).
   *
   * @param array $args
   *   Args to request.
   * @param bool $cacheable
   *   Is it cachable.
   *
   * @return array|bool
   *   Result or null
   */
  public function outwardCodeLookup(array $args, $cacheable = TRUE) {
    return $this->request('outwardCodeLookup', $args, $cacheable);
  }

}
