<?php

namespace App\Stats\Requests;

use App\Stats\Rules\RepositoryFullName;
use Illuminate\Foundation\Http\FormRequest;

class CompareRepositoriesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'firstRepoName'  => ['required', new RepositoryFullName()],
            'secondRepoName' => ['required', new RepositoryFullName()],
        ];
    }
}
