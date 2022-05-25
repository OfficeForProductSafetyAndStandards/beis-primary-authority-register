<?php

namespace Drupal\companies\Plugin\CompaniesRegister;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Interface for Companies Services.
 */
interface CompaniesRegisterInterface {

  /**
   * Get a company by ID.
   *
   * @param string $id
   *   A company ID to be looked up.
   *
   * @return mixed
   *   The company record.
   */
  public function getCompany(string $id): array;

}
