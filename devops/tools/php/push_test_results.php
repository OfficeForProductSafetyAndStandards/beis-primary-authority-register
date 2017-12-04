<?php
require 'vendor/autoload.php';
use PubNub\PubNub;
use PubNub\PNConfiguration;
$pnconf = new PNConfiguration();
$pnconf->setPublishKey(getenv('PUBNUB_PUBLISH_KEY'));
$pnconf->setSubscribeKey(getenv('PUBNUB_SUBSCRIBE_KEY'));
$pubnub = new PubNub($pnconf);
$xml = simplexml_load_file($argv[1]);
$acceptance = json_decode(file_get_contents($argv[2]), true);
$details = [
    'unit' => [
        'failures' => $xml->testsuite[0]['failures'],
        'tests' => $xml->testsuite[0]['tests'],
        'assertions' => $xml->testsuite[0]['assertions'],
        'errors' => $xml->testsuite[0]['errors'],
        'time' => $xml->testsuite[0]['time'],
    ],
    'acceptance' => $acceptance['state'],
];

// convert each item to a string
foreach ($details['unit'] as &$item) {
    $item = "" . $item;
}
$result = $pubnub->publish()
    ->channel('travis_tests')
    ->message($details)
    ->ttl(1800)
    ->sync();
