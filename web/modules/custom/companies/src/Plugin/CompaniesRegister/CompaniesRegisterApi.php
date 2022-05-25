<?php

namespace Drupal\companies\Plugin\CompaniesRegister;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Cache\Cache;
use Http\Adapter\Guzzle6\Client;
use Netsensia\CompaniesHouse\Api\Client\Client as CompaniesHouseClient;

/**
 * Provides a base implementation for a ParSchedule plugin.
 *
 * @see \Drupal\par_actions\ParScheduleInterface
 * @see \Drupal\par_actions\ParScheduleManager
 * @see \Drupal\par_actions\Annotation\CompaniesRegister
 * @see plugin_api
 */
abstract class CompaniesRegisterApi extends PluginBase implements CompaniesRegisterInterface {

  /** @var null An optional php client for handling api requests. */
  protected $client = NULL;

  /**
   * Create the GovUK notify API client.
   */
  public function __construct() {
    $this->client = $this->getClient();
  }

  public function getClient() {
    return NULL;
  }
}
