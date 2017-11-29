<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    private $services;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        \App\Services\PubNubService $pubNubService,
        \App\Services\UptimeRobotStatsService $uptimeRobotStatsService,
        \App\Services\BuildVersionService $buildVersionService,
        \App\Services\TravisStatsService $travisStatsService,
        \App\Services\GitHubStatsService $gitHubStatsService
    )
    {
        $this->services['uptime'] = $uptimeRobotStatsService;
        $this->services['pubnub'] = $pubNubService;
        $this->services['travis'] = $travisStatsService;
        $this->services['github'] = $gitHubStatsService;
        $this->services['build_versions'] = $buildVersionService;
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function cloudFoundryStats()
    {
        return $this->services['pubnub']->stats('cloud_foundry_8');
    }

    public function testStats()
    {
        return $this->services['pubnub']->stats('travis_tests');
    }

    public function uptimeStats()
    {
        return $this->services['uptime']->stats();
    }

    public function travisStats()
    {
        return $this->services['travis']->stats();
    }

    public function gitHubStats()
    {
        return $this->services['github']->stats();
    }

    public function buildVersionStats()
    {
        return $this->services['build_versions']->stats();
    }
}

