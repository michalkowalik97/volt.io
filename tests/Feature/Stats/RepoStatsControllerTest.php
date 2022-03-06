<?php

namespace Tests\Feature\Stats;

use Tests\TestCase;

class RepoStatsControllerTest extends TestCase
{
    public function testCompareRepositories()
    {
        $response = $this->post('/api/compare-repos', [
            "firstRepoName"  => "laravel/laravel",
            "secondRepoName" => "Laravel-Lang/lang",
        ]);

        $response->assertJsonStructure([
            'stats' => [
                '0'      => [
                    "name",
                    "stargazers",
                    "watchers",
                    "forks",
                    "open_prs",
                    "closed_prs",
                    "last_release_at",
                    "size",
                    "open_issues",
                ],
                '1'      => [
                    "name",
                    "stargazers",
                    "watchers",
                    "forks",
                    "open_prs",
                    "closed_prs",
                    "last_release_at",
                    "size",
                    "open_issues",
                ],
                "totals" => [
                    "stargazers"  => [
                        "diff",
                        "percent",
                    ],
                    "watchers"    => [
                        "diff",
                        "percent",
                    ],
                    "forks"       => [
                        "diff",
                        "percent",
                    ],
                    "open_prs"    => [
                        "diff",
                        "percent",
                    ],
                    "closed_prs"  => [
                        "diff",
                        "percent",
                    ],
                    "last_release_at",
                    "size"        => [
                        "diff",
                        "percent",
                    ],
                    "open_issues" => [
                        "diff",
                        "percent",
                    ],
                ],
            ],
        ]);
    }

    public function testCompareRepositoriesWithWrongNames()
    {
        $response = $this->post('/api/compare-repos', [
            "firstRepoName"  => "wrong_repo_name",
            "secondRepoName" => "correct/name",
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['firstRepoName']]);
    }

    public function testCompareRepositoriesWithoutParams()
    {
        $response = $this->post('/api/compare-repos', [
            "secondRepoName" => "correct/name",
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['firstRepoName']]);
    }
}
