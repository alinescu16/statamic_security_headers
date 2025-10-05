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
        'publicDirectory' => 'resources',
        'buildDirectory' => 'build',
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

        $this->app->singleton(SecurityHeadersGradeProvider::class, function ($app) {
            return new SecurityHeadersGradeProvider($app);
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
        Nav::extend(function ($nav) {
            $nav->content('Security Headers')
                ->section('Tools')
                ->route('security_headers.index')
                ->icon('shield-key');
        });
    }
}
