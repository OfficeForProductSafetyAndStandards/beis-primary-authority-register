<?php

namespace Drupal\par_model\Tests;

use Drupal\trance\Tests\TranceAccessTest;

/**
 * Tests basic par_entity access functionality.
 *
 * @group par_entity
 */
class ParEntityAccessTest extends TranceAccessTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['par_entity'];

  /**
   * Entity type id.
   *
   * @var string
   */
  protected $entityTypeId = 'par_entity';

}
