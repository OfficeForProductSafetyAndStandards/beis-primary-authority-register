<?php

namespace App\Http\Controllers;
use Cache;

class IndexController extends Controller
{
    private $services;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        \App\Services\CloudFoundryStatsService $cloudFoundryStatsService,
        \App\Services\UptimeRobotStatsService $uptimeRobotStatsService,
        \App\Services\BuildVersionService $buildVersionService,
        \App\Services\TravisStatsService $travisStatsService,
        \App\Services\GitHubStatsService $gitHubStatsService,
        \App\Services\QueueAndCronStatsService $queueAndCronStatsService
    )
    {
        $this->services['uptime'] = $uptimeRobotStatsService;
        $this->services['cf'] = $cloudFoundryStatsService;
        $this->services['travis'] = $travisStatsService;
        $this->services['github'] = $gitHubStatsService;
        $this->services['build_versions'] = $buildVersionService;
        $this->services['queue_and_cron'] = $queueAndCronStatsService;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome')->withCloudFoundryAppsToDisplay(4);
    }

    public function queueAndCronStats()
    {
        return Cache::remember('queue_and_cron', 2, function () {
            return $this->services['queue_and_cron']->stats();
        });
    }

    public function cloudFoundryStats()
    {
        return Cache::remember('cloud_foundry_stats_service', 1, function () {
            return $this->services['cf']->stats();
        });
    }

    public function testStats()
    {
        return [
            'acceptance' => Cache::get('test_results_acceptance'),
            'unit' => Cache::get('test_results_unit'),
        ];
    }

    public function uptimeStats()
    {
        return Cache::remember('uptime', 1, function () {
            return $this->services['uptime']->stats();
        });
    }

    public function travisStats()
    {
        return Cache::remember('travis', 1, function () {
            return $this->services['travis']->stats();
        });
    }

    public function gitHubStats()
    {
        return Cache::remember('github', 1, function () {
            return $this->services['github']->stats();
        });
    }

    public function buildVersionStats()
    {
        return Cache::remember('build_versions', 1, function () {
            return $this->services['build_versions']->stats();
        });
    }
}

