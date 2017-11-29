<?php

namespace App\Services;

use Config;

class BuildVersionService
{

    public function stats() {
    	$versionInformation = [];

    	foreach (Config::get('dashboard.environments') as $environment) {
    		try {
    			$json = json_decode(file_get_contents($environment['build_version_url'] . '?r=' . rand(1,1000000)));
    			$versionInformation[$environment['name']] = empty($json) ? null : $json->tag;
    		} catch (\Exception $e) {
    			$versionInformation[$environment['name']] = null;
    		}
    	}

        return $versionInformation;
    }
}
