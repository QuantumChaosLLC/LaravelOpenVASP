<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelOpenVasp\Http\Controllers\OpenVaspController;

Route::prefix(config('openvasp.route_prefix', 'api/openvasp'))
    ->middleware(config('openvasp.middleware', ['api']))
    ->group(function (): void {
        Route::get('/health', [OpenVaspController::class, 'health']);

        Route::post('/transfers', [OpenVaspController::class, 'store']);
        Route::get('/transfers/{messageId}', [OpenVaspController::class, 'show']);

        Route::post('/transfers/{messageId}/accept', [OpenVaspController::class, 'accept']);
        Route::post('/transfers/{messageId}/reject', [OpenVaspController::class, 'reject']);
        Route::post('/transfers/{messageId}/settle', [OpenVaspController::class, 'settle']);
        Route::post('/transfers/{messageId}/cancel', [OpenVaspController::class, 'cancel']);
    });
