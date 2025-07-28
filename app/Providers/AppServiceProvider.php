<?php

namespace App\Providers;

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
        //
        $this->app->bind(
            \Ophim\Crawler\OphimCrawler\Controllers\CrawlController::class,
            \App\Http\Controllers\CustomCrawlController::class
        );

        $this->app->bind(
            \Ophim\Crawler\OphimCrawler\Option::class,
            \App\Library\CustomOption::class
        );
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
