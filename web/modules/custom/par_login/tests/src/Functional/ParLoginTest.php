<?php

namespace Drupal\Tests\par_data\Kernel;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Session\AccountInterface;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Tests PAR Login.
 *
 * @group PAR Login
 */
class ParLoginTestBase extends EntityKernelTestBase {

  static $modules = [
    'user',
    'system',
    'field',
    'entity_test'
  ];

  /**
   * @var AccountInterface
   */
  protected $account;

  protected $permissions = [
    'access content',
  ];

  protected $entityTypes = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Must change the bytea_output to the format "escape" before running tests.
    // @see https://www.drupal.org/node/2810049
    //db_query("ALTER DATABASE 'par' SET bytea_output = 'escape';")->execute();
    parent::setUp();

    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * Test the email address is case insensitive.
   *
   * Moving this single to into PAR tests so that the checks for case insensitive
   * login are conducted regardless of whether we have this patch installed...
   * @see https://www.drupal.org/project/drupal/issues/2490294
   */
  public function testCaseInsensitiveEmail() {
    $uppercase_username = 'SENSITİVEKINDĄCASE';
    $lowercase_username = mb_strtolower($uppercase_username);
    $uppercase_email = "$uppercase_username@EXAMPLE.com";
    $lowercase_email = "$lowercase_username@example.com";
    $emailcase_email = "$lowercase_username@ExAmPlE.com";

    $this->drupalCreateUser([], $uppercase_username, ['mail' => $uppercase_email]);

    $uppercase_users = $this->entityTypeManager->getStorage('user')
      ->loadByProperties(['mail' => $uppercase_email]);
    $lowercase_users = $this->entityTypeManager->getStorage('user')
      ->loadByProperties(['mail' => $lowercase_email]);
    $emailcase_users = $this->entityTypeManager->getStorage('user')
      ->loadByProperties(['mail' => $emailcase_email]);

    $this->assertCount(1, $uppercase_users, new FormattableMarkup('The user account was identified using an uppercase email %email', ['%email' => $uppercase_email]));
    $this->assertCount(1, $lowercase_users, new FormattableMarkup('The user account was identified using a lowercase email %email', ['%email' => $lowercase_email]));
    $this->assertCount(1, $emailcase_users, new FormattableMarkup('The user account was identified using a mixed case email %email', ['%email' => $emailcase_email]));

    $this->assertIdentical($uppercase_users, $lowercase_users, new FormattableMarkup('The user accounts loaded by email %email match.', ['%email' => $lowercase_email]));
    $this->assertIdentical($uppercase_users, $emailcase_users, new FormattableMarkup('The user accounts loaded by email %email match.', ['%email' => $emailcase_email]));
  }

}
