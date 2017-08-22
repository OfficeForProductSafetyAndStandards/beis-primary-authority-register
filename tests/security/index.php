<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use Dotenv\Dotenv;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

function readVaultValue($key, $path) {
    exec('vault read -field=' . $key . ' ' . $path, $output);
    return $output[0];
}

$parEnvs = [
    'staging' => [
        'key' => readVaultValue('S3_ACCESS_KEY', 'secret/par/env/staging'),
        'secret' => readVaultValue('S3_SECRET_KEY', 'secret/par/env/staging'),
        'bucket' => 'transform-par-beta-development-private',
        'path' => '/staging/documents/advice',
        'document' => '10.Assured Advice.docx',
    ],
    'production' => [
        'key' => readVaultValue('S3_ACCESS_KEY', 'secret/par/env/production'),
        'secret' => readVaultValue('S3_SECRET_KEY', 'secret/par/env/production'),
        'bucket' => 'transform-par-beta-production-private',
        'path' => '/documents/advice',
        'document' => '10.Assured Advice.docx',
    ],    
];

foreach ($parEnvs as $parEnvName => $parEnv) {
    $client = S3Client::factory([
        'credentials' => [
            'key'    => $parEnv['key'],
            'secret' => $parEnv['secret'],
        ],
        'region' => 'eu-west-1',
        'version' => '2006-03-01',
    ]);
    
    $adapter = new AwsS3Adapter($client, $parEnv['bucket'], $parEnv['path']);
    
    $e = false;
    try {
        $adapter->copy($parEnv['document'], sys_get_temp_dir());
    } catch (Exception $e) {
        $e = true;
    }
    
    if ($e) {
        echo 'FAIL: ' . $parEnvName . ' denied access to own bucket' . PHP_EOL;
    } else {
        echo 'PASS: ' . $parEnvName . ' allowed access to own bucket' . PHP_EOL;
    }
    
    foreach ($parEnvs as $denyEnvName => $denyEnv) {
        if ($parEnvName == $denyEnvName) {
            continue;
        }
        $adapter = new AwsS3Adapter($client, $denyEnv['bucket'], $denyEnv['path']);
        
        $e = false;
        try {
            $adapter->copy($denyEnv['document'], sys_get_temp_dir());
        } catch (Exception $e) {
            $e = true;
        }
        
        if ($e) {
            echo 'PASS: ' . $parEnvName . ' denied access to ' . $denyEnvName . ' bucket' . PHP_EOL;
        } else {
            echo 'FAIL: ' . $parEnvName . ' allowed access to ' . $denyEnvName . ' bucket' . PHP_EOL;
        }
    }
}

