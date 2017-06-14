<?php

namespace Drupal\par_model\Tests;

use Drupal\trance\Tests\TranceTypeTest;

/**
 * Ensures that par_entity type functions work correctly.
 *
 * @group par_entity
 */
class ParEntityTypeTest extends TranceTypeTest {

  /**
   * Entity type id.
   *
   * @var string
   */
  protected $entityTypeId = 'par_entity';

  /**
   * Bundle entity type id.
   *
   * @var string
   */
  protected $bundleEntityTypeId = 'par_entity_type';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['par_entity'];

}
