<?php

namespace Drupal\par_model\Tests;

use Drupal\trance\Tests\TranceEditFormTest;

/**
 * Create a content entity and test edit functionality.
 *
 * @group par_entity
 */
class ParEntityEditFormTest extends TranceEditFormTest {

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
