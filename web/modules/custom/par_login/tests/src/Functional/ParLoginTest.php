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
   * Extending the default login test to take care of the GOV UK sign in form.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  protected function parLogin(AccountInterface $account) {
    if ($this->loggedInUser) {
      $this->drupalLogout();
    }

    $this->drupalGet(Url::fromRoute('user.login'));
    $this->submitForm([
      'name' => $account->getAccountName(),
      'pass' => $account->passRaw,
    ], t('Sign in'));

    // @see ::drupalUserIsLoggedIn()
    $account->sessionId = $this->getSession()->getCookie(\Drupal::service('session_configuration')->getOptions(\Drupal::request())['name']);
    $this->assertTrue($this->drupalUserIsLoggedIn($account), new FormattableMarkup('User %name successfully logged in.', ['%name' => $account->getAccountName()]));

    $this->loggedInUser = $account;
    $this->container->get('current_user')->setAccount($account);
  }

  /**
   * Test the login is case insensitive.
   *
   * Moving this single to into PAR tests so that the checks for case insensitive
   * login are conducted regardless of whether we have this patch installed...
   * @see https://www.drupal.org/project/drupal/issues/2490294
   */
  public function testCaseInsensitiveLogin() {
    $translations = [
      'en' => [
        'Log in' => 'Sign in',
        'Log out' => 'Sign out',
      ]
    ];

    $username = 'SENSITİVEKINDĄCASE';
    $user1 = $this->drupalCreateUser([], $username);
    // Change username to lowercase without saving to test case insensitive login.
    $user1->name = mb_strtolower($username);
    $this->parLogin($user1);

    $this->drupalLogout();
  }

}
