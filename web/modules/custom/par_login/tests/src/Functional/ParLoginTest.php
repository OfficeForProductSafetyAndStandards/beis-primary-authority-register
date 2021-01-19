<?php

namespace Drupal\Tests\user\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\User;

/**
 * Ensure that login works as expected.
 *
 * @group user
 */
class UserLoginTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Test the login is case insensitive.
   *
   * Moving this single to into PAR tests so that the checks for case insensitive
   * login are conducted regardless of whether we have this patch installed...
   * @see https://www.drupal.org/project/drupal/issues/2490294
   */
  public function testCaseInsensitiveLogin() {
    $username = 'SENSITİVEKINDĄCASE';
    $user1 = $this->drupalCreateUser([], $username);
    // Change username to lowercase without saving to test case insensitive login.
    $user1->name = mb_strtolower($username);
    $this->drupalLogin($user1);

    $this->drupalLogout();
  }

}
