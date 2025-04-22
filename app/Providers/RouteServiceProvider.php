<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public const HOME = '/home';

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerApiRoutes();
        $this->registerWebRoutes();
    }

    protected function registerApiRoutes() : void
    {
        Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
    }

    protected function registerWebRoutes() : void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
    }
}
