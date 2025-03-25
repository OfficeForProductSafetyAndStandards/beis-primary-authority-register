<?php

namespace Drupal\Tests\par_actions\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\par_data_test_entity\Plugin\TestSchedulerManager;

/**
 * Tests PAR Actions test base.
 *
 * @group PAR Actions
 */
class ParSchedulerTestBase extends EntityKernelTestBase {

  protected static $modules = ['user', 'language', 'content_translation', 'comment', 'trance', 'par_validation', 'par_data', 'par_data_config', 'par_data_test_entity', 'par_actions', 'datetime', 'link'];

  /**
   * @var AccountInterface $account
   */
  protected AccountInterface $account;

  /**
   * @var TestSchedulerManager $schedulerManager
   */
  protected TestSchedulerManager $schedulerManager;

  protected array $permissions = [
    'access content',
    'access par_data_test_entity entities',
  ];

  protected array $entityTypes = [];

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function setUp(): void {
    // Must change the bytea_output to the format "escape" before running tests.
    // @see https://www.drupal.org/node/2810049
    //db_query("ALTER DATABASE 'par' SET bytea_output = 'escape';")->execute();

    parent::setUp();

    // Setup test scheduler plugin manager.
    $this->schedulerManager = new TestSchedulerManager();

    // Create a new non-admin user.
    $this->account = $this->createUser(['uid' => 2], $this->permissions);
    \Drupal::currentUser()->setAccount($this->account);

    // Mimic some of the functionality in \Drupal\Tests\file\Kernel\FileManagedUnitTestBase
    $this->setUpFilesystem();

    // Install out entity hooks.
    $this->entityTypes = [
      'par_data_entity_test',
    ];

    // Install config for par_data if required.
    $this->installConfig('par_data');
    // Install the test entity feature config
    $this->installConfig('par_data_test_entity');

    // Set up entity schema.
    $this->installEntitySchema('par_data_test_entity');

    // Install file config.
    $this->installConfig(['system']);
    $this->installEntitySchema('user');
  }

  public function getBaseValues(): array {
    return [
      'uid' => $this->account,
      'type' => 'UNKNOWN',
    ];
  }

  public function getTestEntityValues($expiry_date = '2020-01-01'): array {
    return [
      'type' => 'test',
      'expiry_date' => $expiry_date,
    ] + $this->getBaseValues();
  }
}
