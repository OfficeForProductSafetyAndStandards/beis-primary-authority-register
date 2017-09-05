<?php 
// Set flysystem configuration to use local files for all environments,
// and S3 buckets for production files. We also have an artifact bucket
// for database backups and test reports.
$settings['flysystem'] = [
    's3public' => [
        'name' => 'S3 Public',
        'description' => 'The S3 store for public files.',
        'driver' => 'local',
        'config' => [
            'root' => $settings['file_public_path'],
            'public' => TRUE,
        ],
        'cache' => TRUE,
        'serve_js' => TRUE,
        'serve_css' => TRUE,
    ],
    's3private' => [
        'name' => 'S3 Private',
        'description' => 'The S3 store for private files.',
        'driver' => 'local',
        'config' => [
            'root' => $settings['file_private_path'],
        ],
        'cache' => TRUE,
        'serve_js' => FALSE,
        'serve_css' => FALSE,
    ],
    's3backups' => [
        'name' => 'S3 Database Backups',
        'description' => 'The S3 store for database backups.',
        'driver' => 's3',
        'config' => [
            'key'    => getenv('S3_ACCESS_KEY'),
            'secret' => getenv('S3_SECRET_KEY'),
            'region' => 'eu-west-1',
            'bucket' => getenv('S3_BUCKET_ARTIFACTS'),
            'prefix' => 'backups',
        ],
        'cache' => TRUE,
        'serve_js' => FALSE,
        'serve_css' => FALSE,
    ],
];

$config['config_split.config_split.dev_config']['status'] = TRUE;
