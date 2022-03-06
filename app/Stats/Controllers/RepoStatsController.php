<?php

namespace App\Stats\Controllers;

use App\Stats\DTO\RepositoriesRequestDataDTO;
use App\Stats\Requests\CompareRepositoriesRequest;
use App\Stats\Services\StatisticsService;
use Illuminate\Http\JsonResponse;

class RepoStatsController
{

    /**
     * @OA\Post(
     *      path="/api/compare-repos",
     *      summary="Compare repositories data",
     *      description="Compare repositories using thier full names.",
     *
     * @OA\RequestBody(
     *         description="Input parameters format",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstRepoName",
     *                     description="Full name of first repository.",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="secondRepoName",
     *                     description="Full name of second repository.",
     *                     type="string",
     *                 ),
     *              )
     *          )
     *      )
     *      @OA\Response(
     *          response="200",
     *          description="Success response",
     *
     *          @OA\JsonContent(
     *              type="object",
     *              example={"stats":{"0":{"name":"laravel/laravel","stargazers":68933,"watchers":68933,"forks":22292,"open_prs":1,"closed_prs":5834,"last_release_at":"2022-02-22 16:06:11","size":10380,"open_issues":31},"1":{"name":"symfony/symfony","stargazers":26567,"watchers":26567,"forks":8587,"open_prs":218,"closed_prs":45437,"last_release_at":"2022-03-05 21:25:04","size":221370,"open_issues":747},"totals":{"stargazers":{"diff":42366,"percent":259.47},"watchers":{"diff":42366,"percent":259.47},"forks":{"diff":13705,"percent":259.6},"open_prs":{"diff":217,"percent":0.46},"closed_prs":{"diff":39603,"percent":12.84},"last_release_at_diff_days":0,"size":{"diff":210990,"percent":4.69},"open_issues":{"diff":716,"percent":4.15}}}},
     *              @OA\Property(property="stats", type="object", description="Object containing statistics."),
     *          ),
     *      @OA\Response(
     *          response="422",
     *          description="Error response"
     *      )
     * )
     */
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
