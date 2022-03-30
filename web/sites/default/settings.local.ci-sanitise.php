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

$settings['trusted_host_patterns'] = [''];

// Force the index to be created on the database server for CI.
$config['search_api.index.partnership_index']['server'] = 'database_server';

// Ensure ci always runs with the same memory that other environments do.
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 60);
