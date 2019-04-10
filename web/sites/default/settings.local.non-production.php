<?php
// A generic settings file for use on all GovUK PaaS non-production instances.

// Remove url restrictions.
$settings['trusted_host_patterns'] = [''];

// Enable all dev and testing config.
$config['config_split.config_split.dev_config']['status'] = TRUE;
$config['config_split.config_split.test_config']['status'] = TRUE;

// Enable the local services configuration which enables debugging.
if (file_exists($app_root . '/' . $site_path . '/services.local.yml')) {
  $settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.local.yml';
}

// Enable tota11y library.
$settings['enable_tota11y'] = TRUE;
