<?php

namespace Drupal\par_model\Tests;

use Drupal\trance\Tests\TranceOwnerTest;

/**
 * Tests par_entity owner functionality.
 *
 * @group Entity
 */
class ParEntityOwnerTest extends TranceOwnerTest {

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
