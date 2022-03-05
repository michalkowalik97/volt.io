<?php

namespace App\GitHubAPI\Services;

use App\GitHubAPI\Exceptions\ApiServiceException;
use App\GitHubAPI\Interfaces\ApiInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Client\ClientInterface;

class GitHubApi implements ApiInterface
{
    private ClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('repo_api.repo_api_url'),
            'headers'  => [
                'Accept'       => 'application/vnd.github.v3+json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getRepoByName(string $name): array
    {
        return $this->request('GET', 'search/repositories', ['query' => ['q' => $name]]);
    }

    private function request($method, $endpoint, $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
        } catch (ConnectException $e) {
            throw new ApiServiceException('GitHub API is not available.');
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        throw new ApiServiceException($response->getBody());
    }
}
