<?php

namespace App\GitHubAPI\Services;

use App\GitHubAPI\Enums\PullRequestStatusEnum;
use App\GitHubAPI\Exceptions\ApiServiceException;
use App\GitHubAPI\Interfaces\ApiInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientInterface;

class GitHubApi implements ApiInterface
{
    private ClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('repo_api.repo_api_url'),
            'headers'  => [
                'Accept'        => 'application/vnd.github.v3+json',
                'Content-Type'  => 'application/json',
                'Authorization' => $this->getBasicAuthHeaderContent(),
            ],
        ]);
    }

    public function getRepoByName(string $name): array
    {
        return $this->request('GET', 'search/repositories', ['query' => ['q' => $name, 'per_page' => 1]]);
    }

    public function getReleasesDataByUrl(string $releasesUrl, int $page = 1, int $perPage = 30): array
    {
        $url = str_ireplace('{/id}', '', $releasesUrl);

        $response = Http::withHeaders(['Authorization' => $this->getBasicAuthHeaderContent()])->get($url, ['page' => $page, 'per_page' => $perPage]);
        if ($response->status() === 200) {
            return json_decode($response->body(), true);
        }

        Log::info('Unable to get list of releases.', (array)$response->body());
        throw new ApiServiceException('Unable to get list of releases.', 422);
    }

    public function getPullsList(string $pullsUrl, PullRequestStatusEnum $state, int $page = 1, int $perPage = 30): array
    {
        $url = str_ireplace('{/number}', '', $pullsUrl);

        $response = Http::withHeaders(['Authorization' => $this->getBasicAuthHeaderContent()])->get($url, ['state' => $state->value, 'page' => $page, 'per_page' => $perPage]);

        if ($response->status() === 200) {
            return json_decode($response->body(), true);
        }

        Log::info('Unable to get list of pull requests.', (array)$response->body());
        throw new ApiServiceException('Unable to get list of pull requests.', 422);
    }

    private function request($method, $endpoint, $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
        } catch (ConnectException $e) {
            throw new ApiServiceException('GitHub API is not available.');
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new ApiServiceException($response->getBody());
    }

    private function getBasicAuthHeaderContent(): string
    {
        return 'Basic' . base64_encode(config('repo_api.repo_api_username') . ':' . config('repo_api.repo_api_token'));
    }
}
