<?php
namespace Alinandrei\SecurityHeaders\Generators;


use Alinandrei\SecurityHeaders\Clients\SecurityHeadersReportingPlatformClient;
use Alinandrei\SecurityHeaders\Contracts\SecurityHeadersReportingPlatform;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SecurityHeadersPolicyGenerator
{
    /**
     * Holder of the SecurityHeadersReportingPlatform interface
     * 
     */
    protected SecurityHeadersReportingPlatform $reportingPlatformClient;

    /**
     * @param SecurityHeadersReportingPlatform $reportingPlatformClient
     * 
     */
    public function __construct(SecurityHeadersReportingPlatform $reportingPlatform)
    {
        $this->reportingPlatform = $reportingPlatform;
    }

    /**
     * Policy directives fetching and generation
     * 
     * @return array|JsonResponse
     */
    public function generatePolicies() : array|JsonResponse
    {
        $reportsData = $this->reportingPlatform->fetchReportsData();
        
        if (isset($reportsData['status']) || empty($reportsData)) {
            if ( empty($reportsData) ) {
                Log::error('Service Unavailable: Failed to retrieve a valid response from the reporting platform.');
                
                return response()->json( array(
                    'status' => 503,
                    'message' => 'Service Unavailable: Failed to retrieve a valid response from the reporting platform.'
                ), 503);
            }

            return response()->json($reportsData, $reportsData['status']);
        }

        return $this->reportingPlatform->formatReportsData($reportsData);
    }
}