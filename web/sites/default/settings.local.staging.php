<?php

$config['config_split.config_split.dev_config']['status'] = TRUE;

ini_set('memory_limit', '2G');
ini_set('max_execution_time', 600);

// Setting to enable PAR Green header/footer override.
$settings['par_branded_header_footer'] = TRUE;

$settings['hash_salt'] = getenv('PAR_HASH_SALT');

