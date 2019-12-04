<?php

use Drupal\Component\Utility\Random;
use Drupal\Core\Site\Settings;
use Drupal\encrypt\Entity\EncryptionProfile;

/**
 * Implements hook_drush_command().
 */
function par_login_drush_command() {
  $commands = [];
  $commands['encryption-test'] = [
    'description' => 'Test the encryption',
    'aliases' => ['encrypt-test'],
    'arguments' => [
      'encryption_profile' => 'The encryption profile to use',
    ],
    'examples' => [
      'drush encrypt-test tfa_tokens',
    ],
  ];
  return $commands;
}

function drush_par_login_encryption_test($encryption_profile, $text = 'Encryption test string') {
  $encryption_profile = EncryptionProfile::load($encryption_profile);

  if (!$encryption_profile) {
    drush_log(dt('Encryption profile "@name" could not be loaded.', ['@name' => $encryption_profile]), \Drush\Log\LogLevel::ERROR);
    return;
  }

  $encrypted_text = Drupal::service('encryption')->encrypt($text, $encryption_profile);
  $decrypted_text = Drupal::service('encryption')->decrypt($encrypted_text, $encryption_profile);

  if (!empty($decrypted_text) && $decrypted_text === $text) {
    drush_log(dt('Encryption test passed "@text".', ['@text' => $decrypted_text]), \Drush\Log\LogLevel::SUCCESS);
  }
  else {
    drush_log(dt('Encryption test failed "@text".', ['@text' => $decrypted_text]), \Drush\Log\LogLevel::ERROR);
  }
}
