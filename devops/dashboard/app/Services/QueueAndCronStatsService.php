<?php

namespace App\Services;
use GuzzleHttp\Client;

class QueueAndCronStatsService
{
    public function stats() {
        $client = new Client([
		    // Base URI is used with relative requests
            'base_uri' => 'https://primary-authority.beis.gov.uk',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		]);

		$stats = $client->get('par/queue/check.json')->getBody()->getContents();

        return json_decode($stats, true);

    }
}
