<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelOpenVasp\Http\Controllers\OpenVaspController;

Route::prefix(config('openvasp.route_prefix', 'api/openvasp'))
    ->middleware(config('openvasp.middleware', ['api']))
    ->group(function (): void {
        Route::get('/version', [OpenVaspController::class, 'version']);
        Route::get('/identity', [OpenVaspController::class, 'identity']);

        Route::post('/inquiries/{inquiryId}', [OpenVaspController::class, 'inquiry']);
        Route::post('/inquiry-resolutions/{inquiryId}', [OpenVaspController::class, 'inquiryResolution']);
        Route::post('/transfer-confirmations/{inquiryId}', [OpenVaspController::class, 'transferConfirmation']);
    });
