<?php

$config['config_split.config_split.dev_config']['status'] = TRUE;
$config['config_split.config_split.migration_config']['status'] = TRUE;

// Setting to enable PAR Green header/footer override.
$settings['par_branded_header_footer'] = TRUE;

$config['system.mail']['interface']['default'] = 'maillog';
$config['rest_api_authentication.settings']['api_token'] = getenv('JSON_API_KEY');
