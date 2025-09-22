<?php

use Illuminate\Support\Facades\Route;

use \Alinandrei\SecurityHeaders\Http\Controllers\SecurityHeadersController;

Route::get('/security_headers', [SecurityHeadersController::class, 'index'])->name('security_headers.index');

Route::post('/security_headers', [SecurityHeadersController::class, 'store'])->name('security_headers.store');

Route::post('/security_headers/generate_csp', [SecurityHeadersController::class, 'generatePolicies'])->name('security_headers.csp');