<?php

$config['config_split.config_split.dev_config']['status'] = TRUE;
$config['config_split.config_split.test_config']['status'] = TRUE;

// Setting to enable PAR Green header/footer override.
$settings['par_branded_header_footer'] = TRUE;

/**
 * Show all error messages, with backtrace information.
 *
 * @TODO Temporary to resolve staging issues.
 */
$config['system.logging']['error_level'] = 'verbose';
