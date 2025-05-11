<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log all API requests for debugging
        if (config('app.debug')) {
            \Illuminate\Support\Facades\DB::listen(function($query) {
                \Illuminate\Support\Facades\Log::info(
                    $query->sql,
                    ['bindings' => $query->bindings, 'time' => $query->time]
                );
            });
            
            // Log all requests
            \Illuminate\Support\Facades\Request::macro('track', function() {
                if (\Illuminate\Support\Facades\Request::is('search/*')) {
                    $params = \Illuminate\Support\Facades\Request::all();
                    $url = \Illuminate\Support\Facades\Request::fullUrl();
                    $method = \Illuminate\Support\Facades\Request::method();
                    $ip = \Illuminate\Support\Facades\Request::ip();
                    
                    \Illuminate\Support\Facades\Log::info("API REQUEST: $method $url - IP: $ip", ['params' => $params]);
                }
            });
            
            app()->singleton('request.tracker', function () {
                return new class {
                    public function track() {
                        \Illuminate\Support\Facades\Request::track();
                    }
                };
            });
            
            app('request.tracker')->track();
        }
    }
}
