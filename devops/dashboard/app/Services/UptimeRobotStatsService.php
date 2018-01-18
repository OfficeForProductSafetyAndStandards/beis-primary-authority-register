<?php

namespace App\Services;
use GuzzleHttp\Client;
use Config;

class UptimeRobotStatsService
{
    public function stats() {

		$client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'http://api.uptimerobot.com',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		]);

		try {
			$r = $client->post('/v2/getMonitors', [
				'form_params' => Config::get('dashboard.uptime_robot'),
				'allow_redirects' => [
				    'max'             => 5,
				    'strict'          => true,
				    'referer'         => false,
				    'protocols'       => ['http', 'https'],
				    'track_redirects' => true
				]
			]);
		} catch (\Exception $e) {
			dd($e->getMessage());
			return null;
		}

    	return json_decode($r->getBody()->getContents(), true);
    }
}
