<?php
namespace Alinandrei\SecurityHeaders\Clients;

use Alinandrei\SecurityHeaders\Contracts\SecurityHeadersReportingPlatform;

use Alinandrei\SecurityHeaders\Clients\SecurityHeadersReportingPlatformSentryClient;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Container\Container;

class SecurityHeadersReportingPlatformClient
{
    /**
     * The application container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * We no longer need the settings service here, as the container will inject it
     * into the specific client implementations like the SentryClient.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Reporting Platform Client factory
     * 
     * @param string $driver
     */
    public function make(?string $driver): SecurityHeadersReportingPlatform
    {   
        if (is_null($driver)) {
            return null;
        }

        switch (strtolower($driver)) {
            case 'sentry':
                return $this->createSentryClient();
            case 'raygun':
                return $this->createRayGunClient();
            case 'cside':
                return $this->createCSideClient();
            default:
                return $this->createNullClient();
        }
    }

    /**
     * Sentry client
     * 
     */
    protected function createSentryClient(): SecurityHeadersReportingPlatform
    {
        return $this->app->make(SecurityHeadersReportingPlatformSentryClient::class);

    }

    /**
     * TODO:: RayGun client
     * 
     */
    protected function createRayGunClient(): SecurityHeadersReportingPlatform
    {
        // return $this->app->make(SecurityHeadersReportingPlatformRayGunClient::class);
    }

    /**
     * TODO:: c/Side client
     * 
     */
    protected function createCSideClient(): SecurityHeadersReportingPlatform
    {
        // return $this->app->make(SecurityHeadersReportingPlatformCSideClient::class);
    }

    /**
     * NULL client
     * 
     */
    protected function createNullClient(): SecurityHeadersReportingPlatform
    {
        return new class implements SecurityHeadersReportingPlatform {
            public function fetchReportsData(): array
            {
                return ['csp' => ['default-src' => ["'self'"]]];
            }

            public function formatReportsData(): array
            {
                return ['csp' => ['default-src' => ["'self'"]]];
            }
        };
    }
}