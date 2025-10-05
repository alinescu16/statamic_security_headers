<?php

namespace Alinandrei\SecurityHeaders;

use Alinandrei\SecurityHeaders\Http\Middleware\SecurityHeadersMiddleware;
use Alinandrei\SecurityHeaders\Clients\SecurityHeadersReportingPlatformClient;
use Alinandrei\SecurityHeaders\Services\SecurityHeadersServiceProvider;
use Alinandrei\SecurityHeaders\Services\SecurityHeadersGradeProvider;
use Alinandrei\SecurityHeaders\Generators\SecurityHeadersPolicyGenerator;
use Alinandrei\SecurityHeaders\Contracts\SecurityHeadersReportingPlatform;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Preference;
use Statamic\Facades\YAML;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;

class ServiceProvider extends AddonServiceProvider
{
    /**
     * Vite configuration
     * 
     */
    protected $vite = [ 
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
    ]; 

    /**
     * Define routes
     * 
     */
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
        // 'actions' => __DIR__.'/../routes/actions.php',
        // 'web' => __DIR__.'/../routes/web.php',
    ];

    /**
     * Register the middleware that adds the headers to the next request
     * 
     */
    protected $middlewareGroups = [
        'statamic.web' => [
            SecurityHeadersMiddleware::class
        ],
        'web' => [
            SecurityHeadersMiddleware::class
        ],
        // 'cp' => [],
        // 'statamic.cp' => []
    ];

    /**
     * Register in the Service Container the Reporting Platform Client Factory 
     * and Content Security Policy Directives Generator  
     * 
     */
    public function register()
    {
        parent::register();
        
        $this->app->singleton(SecurityHeadersServiceProvider::class, function ($app) {
            return new SecurityHeadersServiceProvider($app);
        });

        $this->app->singleton(SecurityHeaderGradeProvider::class, function ($app) {
            return new SecurityHeaderGradeProvider($app);
        });

        // Register the Reporting Platform Client factory
        $this->app->singleton(SecurityHeadersReportingPlatformClient::class, function ($app) {
            return new SecurityHeadersReportingPlatformClient($app);
        });

        // Decide what Reporting Platform Client will be used
        $this->app->singleton(SecurityHeadersReportingPlatform::class, function ($app) {
            $settingsService = $app->make(SecurityHeadersServiceProvider::class);

            $driver = $settingsService->getReportingPlatformDriver();
            
            return $app->make(SecurityHeadersReportingPlatformClient::class)->make($driver);
        });

        // Register the Policy Generator class
        $this->app->singleton(SecurityHeadersPolicyGenerator::class, function ($app) {
            return new SecurityHeadersPolicyGenerator(
                $app->make(SecurityHeadersReportingPlatform::class)
            );
        });
    }


    /**
     * Add menu to nav bar
     * */
    public function bootAddon()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/dist/build' => public_path('vendor/security_headers/build'),
            ], 'security_headers'); 
        }

        Nav::extend(function ($nav) {
            $nav->content('Security Headers')
                ->section('Tools')
                ->route('security_headers.index')
                ->icon('shield-key');
        });
    }
}
