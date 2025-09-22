<?php

namespace Alinandrei\SecurityHeaders\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\YAML;
use Statamic\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Alinandrei\SecurityHeaders\Generators\SecurityHeadersPolicyGenerator;
use Alinandrei\SecurityHeaders\Services\SecurityHeadersServiceProvider;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SecurityHeadersController extends Controller
{

    /**
     * Load settings and display the main addon view.
     * 
     * @param SecurityHeadersServiceProvider $settingsProvider
     */
    public function index(SecurityHeadersServiceProvider $settingsProvider)
    {
        return view('security_headers::index', [
            'settings' => $settingsProvider->getSettings()
        ]);
    }

    /**
     * Save settings from the Vue component to the YAML file.
     * 
     * @param Request $request
     * @param SecurityHeadersServiceProvider $settingsProvider
     */
    public function store(Request $request, SecurityHeadersServiceProvider $settingsProvider)
    {
        $settingsProvider->saveSettings($request->all());

        return response()->json(['success' => true, 'message' => 'Settings saved successfully.']);
    }

    /**
     * Method for generating policies
     * 
     * @param SecurityHeadersPolicyGenerator $policyGenerator
     * @return array | JsonResponse
     */
    public function generatePolicies(SecurityHeadersPolicyGenerator $policyGenerator) : array | JsonResponse
    {
        return response()->json($policyGenerator->generatePolicies());
    }
}
