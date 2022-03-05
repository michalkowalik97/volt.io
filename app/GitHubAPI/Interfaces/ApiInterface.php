<?php

namespace App\GitHubAPI\Interfaces;

use App\GitHubAPI\Enums\PullRequestStatusEnum;

interface ApiInterface
{
    public function getRepoByName(string $name): array;

    public function getReleasesDataByUrl(string $releasesUrl, int $page = 1, int $perPage = 30): array;

    public function getPullsList(string $pullsUrl, PullRequestStatusEnum $state, int $page = 1, int $perPage = 30): array;
}
