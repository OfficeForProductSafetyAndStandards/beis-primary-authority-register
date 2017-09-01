<?php 

$settings['trusted_host_patterns'] = ['primary-authority.beis.gov.uk'];

// Only use S3 public store when required.
if (getenv('S3_BUCKET_PUBLIC')) {
    $settings['flysystem']['s3public'] = [
        'driver' => 's3',
        'config' => [
            'key'    => getenv('S3_ACCESS_KEY'),
            'secret' => getenv('S3_SECRET_KEY'),
            'region' => 'eu-west-1',
            'bucket' => getenv('S3_BUCKET_PUBLIC'),
        ],
    ] + $settings['flysystem']['s3public'];
}

// Only use S3 private store when required.
if (getenv('S3_BUCKET_PRIVATE')) {
    $settings['flysystem']['s3private'] = [
        'driver' => 's3',
        'config' => [
            'key'    => getenv('S3_ACCESS_KEY'),
            'secret' => getenv('S3_SECRET_KEY'),
            'region' => 'eu-west-1',
            'bucket' => getenv('S3_BUCKET_PRIVATE'),
        ],
    ] + $settings['flysystem']['s3private'];
}