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

// Ensure ci always runs with the same memory that other environments do.
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 60);

$settings['hash_salt'] = 'y9fBHyfS_V90Ubd42vTlAnp0VvZ7Ljgbb68UTNLsBBgXAkGViooWGE59oevcc6UU2X0ObGIIlA';
