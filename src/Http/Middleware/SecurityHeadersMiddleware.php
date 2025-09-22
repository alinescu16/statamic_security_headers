<?php

namespace Alinandrei\SecurityHeaders\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\YAML;

use Alinandrei\SecurityHeaders\Services\SecurityHeadersServiceProvider;

use Illuminate\Support\Facades\Log;


class SecurityHeadersMiddleware
{   
    /**
     * @var SecurityHeadersServiceProvider
     */
    protected $settingsProvider; // 1. Add a class property to hold the service

    /**
     * Create a new middleware instance.
     *
     * @param SecurityHeadersServiceProvider $settingsProvider
     */
    // 2. Add the constructor to inject the dependency
    public function __construct(SecurityHeadersServiceProvider $settingsProvider)
    {
        $this->settingsProvider = $settingsProvider;
    }

    /**
     * The middleware function 
     * 
     * @param Request $request
     * @param Closure $next
     * 
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $settings = $this->settingsProvider->getSettings();

        if (empty($settings)) {
            return $response;
        }

        foreach( $settings as $key => $setting ) {
            if ( isset($setting['enabled']) && (bool) $setting['enabled'] ) {
                switch( $key ) {
                    case 'xFrameOptions':
                        $enabled = $setting['enabled'];
                        $value = $setting['value'];

                        if ( ! $value || ! $enabled ) {
                            break;
                        }

                        if ( strpos($value, '-') ) {
                            $value = preg_replace('/-/', '', $value);
                        }

                        $value = strtoupper($value);

                        $response->headers->set('X-Frame-Options', $value);
                        break;

                    case 'xContentTypeOptions': 
                        $enabled = $setting['enabled'];

                        if ( ! $enabled ) {
                            break;
                        }

                        $response->headers->set('X-Content-Type-Options', 'nosniff');
                        break;

                    case 'strictTransportSecurity':
                        $enabled = $setting['enabled'];

                        $maxAge = $setting['maxAge'] * 31536000;

                        if ( ! $maxAge | ! $enabled ) {

                        }

                        $value = "max-age=$maxAge";

                        if ($setting['includeSubDomains'] ?? false) {
                            $value .= '; includeSubDomains';
                        }

                        if ($setting['preload'] ?? false) {
                            $value .= '; preload';
                        }

                        $response->headers->set('Strict-Transport-Security', $value);
                        break;

                    case 'referrerPolicy':
                        $enabled = $setting['enabled'];

                        $value = $setting['value'];

                        if ( ! $value || ! $enabled ) {
                            break;
                        }

                        $response->headers->set('Referrer-Policy', $value);
                        break;

                    case 'contentSecurityPolicy':
                        $enabled = $setting['enabled'];

                        if ( ! $enabled ) {
                            break;
                        }
                        
                        $value = $setting['policy'];

                        $reporting_platform = $this->settingsProvider->getReportingPlatformDriver();

                        if ($reporting_platform) {

                            $reporting_options = $this->settingsProvider->getReportingPlatformSettings();
                            
                            if ( !empty($reporting_options['reportingUrl']) ) {
                                $value .= " report-uri " . $reporting_options['reportingUrl'] . " ; report-to csp-endpoint";

                                $response->headers->set(
                                    'Report-To', json_encode(array(
                                        'group' => 'csp-endpoint',
                                        'max_age' => 10886400,
                                        'include_subdomains' => true,
                                        'endpoints' => array(
                                            'url' => $reporting_options['reportingUrl']
                                        )
                                    ), JSON_UNESCAPED_SLASHES)
                                );

                                $response->headers->set(
                                    'Reporting-Endpoints', 'csp-endpoint="' . $reporting_options['reportingUrl'] . '"'
                                );
                            }
                        }

                        $response->headers->set( ($setting['reportOnly'] ?? false)
                            ? 'Content-Security-Policy-Report-Only'
                            : 'Content-Security-Policy', $value);
                        break;

                    case 'permissionsPolicy':
                        $value = $setting['policy'];

                        if ( ! $value || ! $enabled ) {
                            break;
                        }
                        
                        $response->headers->set( 'Permissions-Policy', $value );
                        break;
                }
            }
        }

        return $response;
    }
}