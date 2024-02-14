<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Blade;
use App\View\Components\Select2;
use App\View\Components\Select2Tags;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Blade::component('x-jet-components.select', Select2::class);
        Blade::component('x-jet-components.select', Select2Tags::class);
        //Blade::component('x-jet-components.*');

        //LIVE
        ///$this->app->bind('path.public', function() {
        //    return realpath('/home/tmblportal/public_html/dist');
        //});
    }
}
