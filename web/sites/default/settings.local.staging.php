<?php

$config['config_split.config_split.dev_config']['status'] = TRUE;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 120);

// Setting to enable PAR Green header/footer override.
$settings['par_branded_header_footer'] = TRUE;

$settings['hash_salt'] = getenv('HASH_SALT');
