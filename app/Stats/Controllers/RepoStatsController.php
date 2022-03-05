<?php

namespace App\Stats\Controllers;

use App\Stats\DTO\RepositoriesRequestDataDTO;
use App\Stats\Requests\CompareRepositoriesRequest;
use App\Stats\Services\StatisticsService;
use Illuminate\Http\JsonResponse;

class RepoStatsController
{
    public function compare(CompareRepositoriesRequest $request, StatisticsService $statisticsService): JsonResponse
    {
        return response()->json([
            'stats' => $statisticsService->getRepositoriesStatistics(new RepositoriesRequestDataDTO(
                firstRepoName: $request->firstRepoName,
                secondRepoName: $request->secondRepoName,
            )),
        ]);
    }

}
