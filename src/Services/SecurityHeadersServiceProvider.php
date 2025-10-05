<?php

namespace Alinandrei\SecurityHeaders\Services;

use Illuminate\Contracts\Container\Container;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\YAML;
use Exception;


class SecurityHeadersServiceProvider
{
    /**
     * The path to the settings file in Statamic's storage.
     */
    private const SETTINGS_PATH = 'security-headers/settings.yaml';

    /**
     * The key used for caching the parsed settings.
     */
    private const CACHE_KEY = 'alinandrei.security-headers.settings';

    /**
     * The api key env name
     */
    private const API_KEY_NAME = 'REPORTING_API_KEY';
    
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
     * Get the fully parsed settings array from the YAML file.
     *
     * @return array
     */
    public function getSettings(bool $raw = false): array
    {
        $settings = Cache::rememberForever(self::CACHE_KEY, function () {
            if (!Storage::exists(self::SETTINGS_PATH)) {
                return [];
            }

            try {
                $yamlContents = Storage::get(self::SETTINGS_PATH);

                return YAML::parse($yamlContents) ?? [];
            } catch (Exception $e) {
                Log::error('Error parsing security-headers/settings.yaml: ' . $e->getMessage());
                return [];
            }
        });

        if ( ! $raw ) {
            $apiKey = env(self::API_KEY_NAME);

            if ($apiKey) {
                data_set($settings, 'contentSecurityPolicy.reportingPlatform.reportingApiKey', substr($apiKey, 0, 11) . "********************");
            }
        }
        
        return $settings;
    }

    /**
     * Save settings, clear cache
     *
     * @return void
     */
    public function saveSettings($settings) : void
    {
        try {
            $this->storeApiKey($settings['contentSecurityPolicy']['reportingPlatform']['reportingApiKey']);
        } catch (Exception $e) {
            Log::error('Error storing Api Key: ' . $e->getMessage());
        }

        data_set( $settings, 'contentSecurityPolicy.reportingPlatform.reportingApiKey', self::API_KEY_NAME );

        $yamlContents = YAML::dump($settings);

        $this->clearCache();

        Storage::put(self::SETTINGS_PATH, $yamlContents);
    }

    /**
     * Store the Api Key in the env file
     *
     * @return array|null
     */
    private function storeApiKey($apiKey) : void
    {
        $envFilePath = $this->app->environmentFilePath();

        $envFileContents = file_get_contents($envFilePath);

        if ( ! $apiKey ) {
            return;
        }

        $escapedValue = addcslashes($apiKey, '\\$');
        
        $key = self::API_KEY_NAME;
        $keyPattern = "/^{$key}=.*/m";

        if (preg_match($keyPattern, $envFileContents)) {
            if ( ! strpos($escapedValue, "*") ) {
                $newEnvFileContents = preg_replace($keyPattern, "{$key}={$escapedValue}", $envFileContents);
            }
        } else {
            $newEnvFileContents = $envFileContents . "\n{$key}={$escapedValue}";
        }

        if ( isset($newEnvFileContents) ) {
            file_put_contents($envFilePath, $newEnvFileContents);
        }
    }

    /**
     * Get the configuration for the reporting platform.
     *
     * @return array|null
     */
    public function getReportingPlatformSettings(): ?array
    {
        $settings = $this->getSettings(true);
        
        return $settings['contentSecurityPolicy']['reportingPlatform'] ?? null;
    }

    /**
     * Get the driver name for the reporting platform.
     *
     * @return string|null
     */
    public function getReportingPlatformDriver(): ?string
    {
        $platformSettings = $this->getReportingPlatformSettings();

        return $platformSettings['reportingPlatformName'] ?? null;
    }

    /**
     * Forgets the cached settings.
     *
     * This should be called whenever the settings file is updated.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
