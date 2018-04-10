<?php

namespace App\Services;
use GuzzleHttp\Client;

class CloudFoundryStatsService
{
    public function stats() {
        $tokenData = [
            'username' => env('CF_LOGIN_EMAIL'),
            'password' => env('CF_LOGIN_PASSWORD'),
            'grant_type' => 'password',
            'scopes' => '*'
        ];

        $client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'https://' . env('CF_LOGIN_ENDPOINT'),
		    // You can set any number of default request options.
		    'timeout'  => 10.0,
            'headers' => [
                'Authorization' => 'Basic Y2Y6'
            ],
            'form_params' => $tokenData,
		]);

		$token = json_decode($client->post('oauth/token')->getBody()->getContents())->access_token;

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://' . env('CF_ENDPOINT'),
            // You can set any number of default request options.
            'timeout'  => 10.0,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $appsContent = json_decode($client->get('v2/apps')->getBody()->getContents());

        $resources = $appsContent->resources;

        foreach ($resources as $resource) {
            if ($resource->entity->name == 'par-beta-production') {
                $guid = $resource->metadata->guid;
            }
        }

        $statsContent = json_decode($client->get('v2/apps/' . $guid . '/stats')->getBody()->getContents());

        return [
            'received_at' => time(),
            'message' => $statsContent,
        ];

    }
}


