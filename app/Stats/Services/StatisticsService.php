<?php

namespace App\Stats\Services;

use App\GitHubAPI\Enums\PullRequestStatusEnum;
use App\GitHubAPI\Exceptions\ApiServiceException;
use App\GitHubAPI\Interfaces\ApiInterface;
use App\Stats\DTO\RepositoriesRequestDataDTO;
use App\Stats\Exceptions\StatisticsServiceException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StatisticsService
{
    public function __construct(private ApiInterface $api) { }

    public function getRepositoriesStatistics(RepositoriesRequestDataDTO $requestDataDTO): array
    {
        $firstRepoData = $this->getBaseRepoDataFromApi($requestDataDTO->firstRepoName);
        $secondRepoData = $this->getBaseRepoDataFromApi($requestDataDTO->secondRepoName);

        $firstRepoData = $this->appendAdditionalData($firstRepoData);
        $secondRepoData = $this->appendAdditionalData($secondRepoData);

        return $this->compareRepositories([$firstRepoData, $secondRepoData]);
    }

    private function getBaseRepoDataFromApi(string $repoName): array
    {
        try {
            $repoData = $this->api->getRepoByName($repoName);
        } catch (ApiServiceException $e) {
            Log::error('Api service exception: ' . $e->getMessage());
            throw new StatisticsServiceException('Unable  to connect with api. Repository name:' . $repoName, 422);
        }

        if ($repoData['total_count'] === 0) {
            throw new StatisticsServiceException('Repository "' . $repoName . '" not found.', 404);
        }

        if (isset($repoData['items'][0]) === false) {
            Log::info('Invalid data from api.', $repoData);
            throw new StatisticsServiceException('Data returned by API is invalid.', 422);
        }

        if ($repoData['items'][0]['private']) {
            throw new StatisticsServiceException('Repository "' . $repoName . '" isn\'t public.', 422);
        }

        return $repoData['items'][0];
    }

    private function compareRepositories(array $repositories): array
    {
        $totals = [];
        foreach ($repositories[0] as $key => $repository) {
            if ($key === 'name') {
                continue;
            }
            if (stripos($key, '_at') !== false) {
                if ($repositories[0][$key] === null || $repositories[1][$key] === null) {
                    $totals[$key] = 'n/a';
                    continue;
                }
                $date1 = new Carbon($repositories[0][$key]);
                $date2 = new Carbon($repositories[0][$key]);
                $totals[$key] = $date1->diffInDays($date2);
                continue;
            }

            $totals[$key]['diff'] = abs($repositories[0][$key] - $repositories[1][$key]);
            if ($repositories[0][$key] === 0 || $repositories[1][$key] === 0) {
                $totals[$key]['percent'] = 0;
                continue;
            }
            $totals[$key]['percent'] = ($repositories[0][$key] / $repositories[1][$key]) * 100;
            $totals[$key]['percent'] = round($totals[$key]['percent'], 2);
        }

        return array_merge($repositories, ['totals' => $totals]);
    }

    private function repoDataToArray(array $repoData): array
    {

        return [
            'name'            => $repoData['full_name'],
            'stargazers'      => $repoData['stargazers_count'],
            'watchers'        => $repoData['watchers_count'],
            'forks'           => $repoData['forks_count'],
            'open_prs'        => $repoData['open_prs'],
            'closed_prs'      => $repoData['closed_prs'],
            'last_release_at' => $repoData['last_release_at'],
            'size'            => $repoData['size'],
            'open_issues'     => $repoData['open_issues_count'],
        ];
    }

    private function appendAdditionalData(array $repoData): array
    {
        $repoData['open_prs'] = $this->getOpenPRCount($repoData['pulls_url']);
        $repoData['closed_prs'] = $this->getNumberOfPRs($repoData['pulls_url']) - $repoData['open_prs'];

        $repoData['last_release_at'] = $this->getLastReleaseDate($repoData['releases_url']);

        return $this->repoDataToArray($repoData);
    }

    private function getOpenPRCount(string $pullsUrl): int
    {
        $count = 0;
        $page = 1;
        do {
            $response = $this->api->getPullsList($pullsUrl, PullRequestStatusEnum::OPEN, $page++, 100);

            $count += count($response);
        } while (empty($response) === false);

        return $count;
    }

    private function getNumberOfPRs(string $pullsUrl): int
    {
        $response = $this->api->getPullsList($pullsUrl, PullRequestStatusEnum::ALL, perPage: 1);

        if (empty($response)) {
            return 0;
        }

        return $response[0]['number'] ?? 0;
    }

    private function getLastReleaseDate(string $releasesUrl): ?string
    {
        $releases = $this->api->getReleasesDataByUrl($releasesUrl, perPage: 1);

        if (count($releases) === 0) {
            return null;
        }

        return (new Carbon($releases[0]['published_at']))->format('Y-m-d H:i:s');
    }

}
