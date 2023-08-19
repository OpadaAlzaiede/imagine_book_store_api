<?php

namespace App\Providers;

use App\Http\Controllers\Api\V1\Admin\AdminBookController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Services\BookGenres\BookGenreService;
use App\Http\Services\BookGenres\EloquentBookGenreService;
use App\Http\Services\Books\BookModificationService;
use App\Http\Services\Books\BookQueryService;
use App\Http\Services\Books\EloquentAdminBookService;
use App\Http\Services\Books\EloquentUserBookService;
use App\Http\Services\Users\EloquentUserService;
use App\Http\Services\Users\UserService;
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
        $this->app->bind(BookModificationService::class, EloquentAdminBookService::class);


        $this->app->when(AdminBookController::class)
                    ->needs(BookQueryService::class)
                    ->give(EloquentAdminBookService::class);

        $this->app->when(BookController::class)
                    ->needs(BookQueryService::class)
                    ->give(EloquentUserBookService::class);
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
