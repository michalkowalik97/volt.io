<?php

namespace App\Providers;

use App\GitHubAPI\Interfaces\ApiInterface;
use App\GitHubAPI\Services\GitHubApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ApiInterface::class, GitHubApi::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
