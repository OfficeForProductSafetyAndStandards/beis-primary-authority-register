<?php

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
