<?php

// Configure the database.
$databases['default']['default'] = array (
  'database' => 'par',
  'username' => 'par',
  'password' => '123456',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '5432',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
  'driver' => 'pgsql',
);

$settings['trusted_host_patterns'] = [''];

$settings['config_readonly'] = FALSE;

$settings['skip_permissions_hardening'] = TRUE;

$config['config_split.config_split.dev_config']['status'] = TRUE;
$config['config_split.config_split.test_config']['status'] = TRUE;

if (file_exists($app_root . '/' . $site_path . '/services.local.yml')) {
  $settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.local.yml';
}

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

// Enable tota11y library.
$settings['enable_tota11y'] = TRUE;
