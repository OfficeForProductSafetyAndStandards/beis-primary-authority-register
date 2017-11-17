<?php

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

$databases['default']['default'] = array (
  'database' => 'par',
  'username' => 'par',
  'password' => '123456',
  'prefix' => '',
  'host' => 'db',
  'port' => '5432',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
  'driver' => 'pgsql',
);

$settings['trusted_host_patterns'] = [''];

$settings['config_readonly'] = FALSE;

$config['config_split.config_split.dev_config']['status']= TRUE;

if (file_exists($app_root . '/' . $site_path . '/services.local.yml')) {
  $settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.local.yml';
}

// Ensure travis always runs with the same memory that other environments do.
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300);
