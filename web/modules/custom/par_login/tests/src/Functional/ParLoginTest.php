<?php

namespace Drupal\Tests\par_login\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\User;

/**
 * Ensure that login works as expected.
 *
 * This test is part of the case insensitive login patch. Adding the test
 * directly to PAR ensures that this functionality is tested regardless of
 * whether the patch has been applied.
 * @see https://www.drupal.org/project/drupal/issues/2490294
 *
 * @group user
 */
class UserLoginTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['email_registration'];

  /**
   * Test the login is case insensitive.
   *
   * Moving this single to into PAR tests so that the checks for case insensitive
   * login are conducted regardless of whether we have this patch installed...
   * @see https://www.drupal.org/project/drupal/issues/2490294
   */
  public function testCaseInsensitiveLogin() {
    $username = 'SENSITİVEKINDĄCASE';
    $email = "$username@EXAMPLE.com";
    $user1 = $this->drupalCreateUser([], $username, ['mail' => $email]);
    // Change username to lowercase without saving to test case insensitive login.
    $user1->name = mb_strtolower($username);
    $this->drupalLogin($user1);

    $this->drupalLogout();
  }

}
