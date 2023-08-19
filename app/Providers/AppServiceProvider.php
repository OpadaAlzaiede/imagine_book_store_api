<?php

namespace App\Providers;

use App\Http\Services\BookGenres\BookGenreService;
use App\Http\Services\BookGenres\EloquentBookGenreService;
use App\Http\Services\Users\EloquentUserService;
use App\Http\Services\Users\UserService;
use Illuminate\Support\Facades\App;
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
        $this->app->bind(UserService::class, EloquentUserService::class);
        $this->app->bind(BookGenreService::class, EloquentBookGenreService::class);
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
