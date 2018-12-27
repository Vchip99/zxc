<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapMentorRoutes();

        $this->mapClientuserRoutes();

        $this->mapClientRoutes();

        $this->mapAdminRoutes();
        //
    }


      /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::group([
            'middleware' => ['web', 'admin', 'auth:admin'],
            'prefix' => 'admin',
            'as' => 'admin.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }


    /**
     * Define the "client" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapClientRoutes()
    {
        Route::group([
            'middleware' => ['web', 'client', 'auth:client'],
            'domain' => 'client.' . env('APP_DOMAIN'),
            'as' => 'client.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/client.php');
        });
    }

    /**
     * Define the "clientuser" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapClientuserRoutes()
    {
        Route::group([
            'middleware' => ['web', 'clientuser', 'auth:clientuser'],
            'domain' => 'clientuser.' . env('APP_DOMAIN'),
            'as' => 'clientuser.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/clientuser.php');
        });
    }

    /**
     * Define the "mentor" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapMentorRoutes()
    {
        Route::group([
            'middleware' => ['web', 'mentor', 'auth:mentor'],
            'prefix' => 'mentor',
            'as' => 'mentor.',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/mentor.php');
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}
