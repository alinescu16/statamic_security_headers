<?php

namespace Alinandrei\SecurityHeaders\Contracts;

use Illuminate\Http\JsonResponse;
/**
 * Interface for all reporting platform clients.
 */
interface SecurityHeadersReportingPlatform
{
    /**
     * Fetches the latest reports data from the reporting platform.
     *
     * @return array 
     */
    public function fetchReportsData(): array;

    /**
     * Formats the reports data
     *
     * @return array|JsonResponse
     */
    public function formatReportsData( array $array ): array | JsonResponse;
}
