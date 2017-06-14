<?php

namespace Drupal\par_model\Tests;

use Drupal\trance\Tests\TranceCreationTest;

/**
 * Create a content entity and test saving it.
 *
 * @group par_entity
 */
class ParEntityCreationTest extends TranceCreationTest {

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
