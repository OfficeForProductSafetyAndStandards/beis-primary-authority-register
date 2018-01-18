<?php

namespace App\Services;

class DashboardStatsService
{
    private $cloudFoundryStatsService;
    private $buildVersionService;
    private $travisStatsService;
    private $gitHubStatsService;
    private $uptimeRobotStatsService;

    public function __construct(
        CloudFoundryStatsService $cloudFoundryStatsService,
        BuildVersionService $buildVersionService,
        TravisStatsService $travisStatsService,
        GitHubStatsService $gitHubStatsService,
        UptimeRobotStatsService $uptimeRobotStatsService
    )
    {
        $this->cloudFoundryStatsService = $cloudFoundryStatsService;
        $this->buildVersionService = $buildVersionService;
        $this->travisStatsService = $travisStatsService;
        $this->gitHubStatsService = $gitHubStatsService;
        $this->uptimeRobotStatsService = $uptimeRobotStatsService;
    }

    public function stats() {
        return [
            //'github' => $this->gitHubStatsService->stats(),
            //'travis' => $this->travisStatsService->stats(),
            'build_versions' => $this->buildVersionService->stats(),
            'uptime' => $this->uptimeRobotStatsService->stats(),
            'cloudfoundry' => $this->cloudFoundryStatsService->stats(),
        ];
    }
}
