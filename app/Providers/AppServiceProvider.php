<?php

namespace App\Providers;

use App\Services\Guardian;
use App\Services\NewsAPI;
use App\Services\NewsCred;
use App\Services\NewYorkTimes;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsAPI::class, function ($app) {
            return new NewsAPI(config('news_api.news_api'));
        });

        $this->app->bind(NewYorkTimes::class, function ($app) {
            return new NewYorkTimes(config('news_api.new_york_times'));
        });

        $this->app->bind(Guardian::class, function ($app) {
            return new Guardian(config('news_api.guardian'));
        });
        $this->app->bind(NewsCred::class, function ($app) {
            return new NewsCred(config('news_api.NewsCred'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
