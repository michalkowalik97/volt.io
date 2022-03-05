<?php

namespace App\GitHubAPI\Enums;

enum PullRequestStatusEnum: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ALL = 'all';
}
