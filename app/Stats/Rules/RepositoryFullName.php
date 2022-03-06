<?php

namespace App\Stats\Rules;

use Illuminate\Contracts\Validation\Rule;

class RepositoryFullName implements Rule
{
    public function passes($attribute, $value)
    {
        return (bool)preg_match('/.+\/.+/', $value);
    }

    public function message()
    {
        return 'Invalid repository name. You should give full repository name eg.: laravel/laravel.';
    }

}
