<?php

namespace Alinandrei\SecurityHeaders\Clients;

use Alinandrei\SecurityHeaders\Contracts\SecurityHeadersReportingPlatform;
use Alinandrei\SecurityHeaders\Services\SecurityHeadersServiceProvider;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class SecurityHeadersReportingPlatformSentryClient implements SecurityHeadersReportingPlatform
{
    /**
     * The security settings service instance.
     *
     * @var \Alinandrei\SecurityHeaders\Services\SecuritySettingsService
     */
    private $settingsService;

    /**
     * Inject the settings service via the constructor.
     * Laravel's service container will automatically resolve this for you.
     *
     * @param \Alinandrei\SecurityHeaders\Services\SecuritySettingsService $settingsService
     */
    public function __construct(SecurityHeadersServiceProvider $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Get the Reports Data
     * 
     * @return array
     */
    public function fetchReportsData(): array 
    {
        $settings = $this->settingsService->getReportingPlatformSettings();

        if (empty($settings['reportingApiKey']) || empty($settings['reportingOrganization']) || empty($settings['reportingProject'])) {
            Log::error('Sentry reporting client is missing required settings (token, organization, or project).');
            
            return array(
                'code' => 500,
                'message' => 'Sentry reporting client is missing required settings (token, organization, or project).',
            );;
        }

        $baseUrl = rtrim('https://sentry.io/api/0', '/');

        $url = "{$baseUrl}/projects/{$settings['reportingOrganization']}/{$settings['reportingProject']}/events/?statsPeriod=7d";

        try {
            $response = Http::withToken(env($settings['reportingApiKey']))
                ->acceptJson()
                ->get($url);
            
            if ($response->failed()) {
                Log::error('Failed to fetch reports from Sentry API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return array( 
                    'status' => $response->status(),
                    'body' => $response->body(), 
                );
            }

            return $response->json();
        } catch (Exception $e) {
            Log::critical('An exception occurred while calling the Sentry API.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return array( 
                'status' => 500,
                'body' => $e->getMessage(), 
            );
        }

        return array();
    }

    /**
     * Format the Reports Data
     * 
     * @param array $data
     * 
     * @return array
     */
    public function formatReportsData(array $data) : array | JsonResponse
    {   
        return collect($data)
            ->map(function ($report) {
                
                $effectiveDirectiveTag = collect($report['tags'])->firstWhere('key', 'effective-directive');
                $directive = $effectiveDirectiveTag['value'] ?? $report['metadata']['directive'] ?? 'unknown';

                $blockedUriTag = collect($report['tags'])->firstWhere('key', 'blocked-uri');
                $blockedUri = $blockedUriTag['value'] ?? $report['location'] ?? 'unknown';

                if ($blockedUri === 'inline' || $blockedUri === 'inline:') {
                    $blockedUri = "'unsafe-inline'";
                } elseif ($blockedUri === 'eval' || $blockedUri === 'eval:') {
                    $blockedUri = "'unsafe-eval'";
                } elseif ($blockedUri === 'data' || $blockedUri === 'data:') {
                    $blockedUri = "data:";
                } elseif (str_starts_with($blockedUri, 'http')) {
                    $host = parse_url($blockedUri, PHP_URL_HOST);
                    if ($host) {
                        $blockedUri = $host;
                    }
                }
                
                $key = $directive . '|' . $blockedUri;

                return [
                    'key' => $key,
                    'directive' => $directive,
                    'blocked_uri' => $blockedUri,
                ];
            })
            ->groupBy('key')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'directive' => $first['directive'],
                    'blocked_uri' => $first['blocked_uri'],
                ];
            })
            ->values()
            ->all();
    }
}