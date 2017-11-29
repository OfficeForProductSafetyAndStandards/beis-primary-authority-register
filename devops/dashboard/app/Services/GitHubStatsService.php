<?php

namespace App\Services;
use GuzzleHttp\Client;

class GitHubStatsService
{
    public function stats() {

    	$client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'https://api.github.com',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		]);

		$commits = $client->get('/repos/UKGovernmentBEIS/beis-primary-authority-register/commits')->getBody()->getContents();
		$pullRequests = $client->get('/repos/UKGovernmentBEIS/beis-primary-authority-register/pulls')->getBody()->getContents();

        return [
        	'commits' => json_decode($commits),
        	'pull_requests' => json_decode($pullRequests),
        ];
    }
}
