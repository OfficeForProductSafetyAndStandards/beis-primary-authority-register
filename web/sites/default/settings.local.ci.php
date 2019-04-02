<?php

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

// Files need to be stored locally.
//$settings['flysystem']['s3public'] = [
//  'name' => 'S3 Public',
//  'description' => 'The S3 store for public files.',
//  'driver' => 'local',
//  'config' => [
//    'root' => 'sites/default/files/flysystem',
//    'public' => TRUE,
//  ],
//  'serve_js' => TRUE,
//  'serve_css' => TRUE,
//];
//$settings['flysystem']['s3private'] = [
//  'name' => 'S3 Private',
//  'description' => 'The S3 store for private files.',
//  'driver' => 'local',
//  'config' => [
//    'root' => '/var/www/html/private',
//  ],
//  'serve_js' => FALSE,
//  'serve_css' => FALSE,
//];

$settings['trusted_host_patterns'] = [''];

$settings['config_readonly'] = FALSE;

$settings['skip_permissions_hardening'] = TRUE;

$config['config_split.config_split.dev_config']['status'] = TRUE;
$config['config_split.config_split.test_config']['status'] = TRUE;

if (file_exists($app_root . '/' . $site_path . '/services.local.yml')) {
  $settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.local.yml';
}

// Ensure travis always runs with the same memory that other environments do.
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300);

// Enable tota11y library.
$settings['enable_tota11y'] = TRUE;
