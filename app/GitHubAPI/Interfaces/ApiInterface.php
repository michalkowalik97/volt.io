<?php

namespace App\GitHubAPI\Interfaces;

interface ApiInterface
{
    public function getRepoByName(string $name): array;
}
