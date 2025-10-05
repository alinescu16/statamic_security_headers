<?php

namespace Alinandrei\SecurityHeaders\Services;

use Illuminate\Contracts\Container\Container;

use Exception;

use Illuminate\Support\Facades\Http;


class SecurityHeadersGradeProvider
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
     * Get website's grade from MDN's observatory
     * 
     * @return array
     */
    public function getGrade(): array
    {
        $baseUrl =  rtrim('https://observatory-api.mdn.mozilla.net', '/');
        $appUrl = env('APP_URL');
        
        if ( ! $appUrl && strpos($appUrl, 'localhost') && strpos($appurl, '127.0.0.1') ) {
            return array(
                'message' => 'App url not set. Can not fetch grades from the Observatory.',
                'status' => 500
            );
        }
        
        $url = "{$baseUrl}/api/v2/scan?host=" .parse_url($appUrl, PHP_URL_HOST);

        try {
            $response = Http::acceptJson()
                ->post($url);

            return $response->json();
        } catch ( Exception $e ) {
            return array(
                'message' => $e->getMessage(),
                'status' => 500
            );
        }
        
        
        return array('grade' => '1');
    }
}