<?php

namespace App\Services;
use GuzzleHttp\Client;

class TravisStatsService
{
    public function stats() {
        $client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'https://api.travis-ci.org',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		]);

		$summary = $client->get('repos/UKGovernmentBEIS/beis-primary-authority-register')->getBody()->getContents();
		$builds = $client->get('repos/UKGovernmentBEIS/beis-primary-authority-register/builds')->getBody()->getContents();

		foreach (json_decode($builds) as $build) {
			if ($build->branch == 'master' && $build->state == 'finished') {
				$lastCompletedBuildOnMasterBranch = $build->id;
				break;
			}
		}

		$lastBuild = $client->get('repos/UKGovernmentBEIS/beis-primary-authority-register/builds/' . $lastCompletedBuildOnMasterBranch)->getBody()->getContents();

        return [
        	'summary' => json_decode($summary),
        	'builds' => json_decode($builds),
        	'last_completed_build_on_master_branch' => json_decode($lastBuild),
        ];
    }
}
