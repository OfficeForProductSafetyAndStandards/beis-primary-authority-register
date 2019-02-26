<?php
// A generic settings file for use on all GovUK PaaS non-production instances.

$settings['trusted_host_patterns'] = [''];

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
