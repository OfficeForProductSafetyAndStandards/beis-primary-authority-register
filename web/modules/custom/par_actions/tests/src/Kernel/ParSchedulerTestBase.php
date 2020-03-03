<?php

namespace Drupal\Tests\par_actions\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data_test_entity\Entity\ParDataTestEntity;

/**
 * Tests PAR Actions test base.
 *
 * @group PAR Actions
 */
class ParSchedulerTestBase extends EntityKernelTestBase {

  static $modules = ['language', 'content_translation', 'comment', 'trance', 'par_validation', 'par_data', 'par_data_config', 'par_data_test_entity', 'address', 'datetime'];

  /**
   * @var AccountInterface
   */
  protected $account;

  /** @var  \Drupal\par_actions\ParSchedulerRuleInterface */
  protected $scheduler;

  protected $permissions = [
    'access content',
    'bypass file access',
    'access par_data_test_entity entities',
  ];

  protected $entityTypes = [];

  public function getScheduleManager() {
    return \Drupal::service('plugin.manager.par_scheduler');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    // @see https://www.drupal.org/node/2810049
    //db_query("ALTER DATABASE 'par' SET bytea_output = 'escape';")->execute();

    parent::setUp();

    // Create a new non-admin user.
    $this->account = $this->createUser(['uid' => 2], $this->permissions);
    \Drupal::currentUser()->setAccount($this->account);

    // Mimic some of the functionality in \Drupal\Tests\file\Kernel\FileManagedUnitTestBase
    $this->setUpFilesystem();

    // Install out entity hooks.
    $this->entityTypes = [
      'par_data_entity_test',
    ];

    foreach ($this->entityTypes as $type) {
      // Set up schema for par_data.
      $this->installEntitySchema($type);
    }

    // Install config for par_data if required.
    $this->installConfig('par_data');

    // Create the entity bundles required for testing.
    $type = ParDataTestEntity::create([
      'id' => 'test',
      'label' => 'Test',
    ]);
    $type->save();

    // Install the feature config
    $this->installConfig('par_data_test_entity');

    // Install file config.
    $this->installConfig(['system']);
    $this->installEntitySchema('user');
  }

  public function getBaseValues() {
    return [
      'uid' => $this->account,
      'type' => 'UNKNOWN',
    ];
  }

  public function getTestEntityValues($expiry_date = '2020-01-01') {
    return [
      'type' => 'test',
      'expiry_date' => $expiry_date,
    ] + $this->getBaseValues();
  }
}
