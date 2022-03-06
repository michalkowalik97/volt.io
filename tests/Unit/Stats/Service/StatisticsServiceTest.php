<?php

namespace Tests\Unit\Stats\Service;

use App\Stats\DTO\RepositoriesRequestDataDTO;
use App\Stats\Services\StatisticsService;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    private StatisticsService $statisticsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statisticsService = $this->app->make(StatisticsService::class);
    }

    public function testGetRepositoriesStatistics()
    {
        $result = $this->statisticsService->getRepositoriesStatistics(new RepositoriesRequestDataDTO(
            firstRepoName: 'laravel/laravel',
            secondRepoName: 'Laravel-Lang/lang'
        ));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('0', $result);
        $this->assertArrayHasKey('1', $result);
        $this->assertArrayHasKey('totals', $result);
        $this->assertEquals('laravel/laravel', $result[0]['name']);
        $this->assertEquals('Laravel-Lang/lang', $result[1]['name']);
    }
}
