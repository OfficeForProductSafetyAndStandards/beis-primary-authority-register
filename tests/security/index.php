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

function textOut($colourCode, $message) {
    printf("\x1b[" . $colourCode . "m");
    echo $message;
    printf("\x1b[0m");
    echo PHP_EOL;
}

function errorOut($message) {
    textOut(31, $message);
}

function warningOut($message) {
    textOut(33, $message);
}

function passOut($message) {
    textOut(32, $message);
}

function out($user, $bucketOwner, $isDenied) {
    if ($user == $bucketOwner) {
        if ($isDenied) {
            errorOut($user . ' wrongly denied access to own bucket');
        } else {
            passOut($user . ' correctly granted access to own bucket');
        }
        return;
    }
    
    if ($user == 'production' || $bucketOwner == 'production') {
        if ($isDenied) {
            passOut($user . ' correctly denied access to ' . $bucketOwner . ' bucket');
        } else {
            errorOut($user . ' wrongly granted access to ' . $bucketOwner . ' bucket');
        }
        return;
    }
    
    if ($isDenied) {
        warningOut($user . ' denied access to ' . $bucketOwner . ' bucket');
    } else {
        warningOut($user . ' granted access to ' . $bucketOwner . ' bucket');
    }
}

$parEnvs = [
    'production' => [
        'bucket' => readVaultValue('S3_BUCKET_PRIVATE', 'secret/par/env/' . $parEnvName),
        'path' => '/documents/advice',
        'document' => '10.Assured Advice.docx',
    ],    
];

foreach (['staging', 'demo', 'branch', 'continuous'] as $parEnvName) {
    $parEnvs[$parEnvName] = [
        'bucket' => readVaultValue('S3_BUCKET_PRIVATE', 'secret/par/env/' . $parEnvName),
        'path' => '/' . $parEnvName . '/documents/advice',
        'document' => '10.Assured Advice.docx',
    ];
}

foreach ($parEnvs as $parEnvName => $parEnv) {
    $client = S3Client::factory([
        'credentials' => [
            'key'    => readVaultValue('S3_ACCESS_KEY', 'secret/par/env/' . $parEnvName),
            'secret' => readVaultValue('S3_SECRET_KEY', 'secret/par/env/' . $parEnvName),
        ],
        'region' => 'eu-west-1',
        'version' => '2006-03-01',
    ]);
    
    $adapter = new AwsS3Adapter($client, $parEnv['bucket'], $parEnv['path']);
    
    $isDenied = false;
    try {
        $adapter->copy($parEnv['document'], sys_get_temp_dir());
    } catch (Exception $e) {
        $isDenied = true;
    }
    
    out($parEnvName, $parEnvName, $isDenied);
    
    foreach ($parEnvs as $denyEnvName => $denyEnv) {
        if ($parEnvName == $denyEnvName) {
            continue;
        }
        $adapter = new AwsS3Adapter($client, $denyEnv['bucket'], $denyEnv['path']);
        
        $isDenied = false;
        try {
            $adapter->copy($denyEnv['document'], sys_get_temp_dir());
        } catch (Exception $e) {
            $isDenied = true;
        }
        
        out($parEnvName, $denyEnvName, $isDenied);
    }
}

