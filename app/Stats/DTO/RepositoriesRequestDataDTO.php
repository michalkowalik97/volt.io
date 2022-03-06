<?php

namespace App\Stats\DTO;

class RepositoriesRequestDataDTO
{
    public function __construct(
        public readonly string $firstRepoName,
        public readonly string $secondRepoName
    ) {
    }
}
