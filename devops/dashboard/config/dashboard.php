<?php

return [
    'pubnub' => [
        'subscribe_key' => env('BEIS_PAR_PUBNUB_SUBSCRIBE_KEY'),
        'publish_key' => env('BEIS_PAR_PUBNUB_PUBLISH_KEY'),
    ],
    'environments' => [
        ['name' => 'production', 'build_version_url' => 'https://primary-authority.beis.gov.uk/build_version.txt'],
        ['name' => 'staging', 'build_version_url' => 'https://staging-cdn.par-beta.co.uk/build_version.txt'],
        ['name' => 'assessment', 'build_version_url' => 'https://assessment-cdn.par-beta.co.uk/build_version.txt'],
        ['name' => 'branch', 'build_version_url' => 'https://branch-cdn.par-beta.co.uk/build_version.txt'],
        ['name' => 'demo', 'build_version_url' => 'https://demo-cdn.par-beta.co.uk/build_version.txt'],
    ],
    'uptime_robot' => [
        'api_key' => env('UPTIME_ROBOT_API_KEY'),
        'monitors' => '779460318',
        'logs' => '1',
        'custom_uptime_ratios' => '7',
        'response_times' => '1',
    ]
];
