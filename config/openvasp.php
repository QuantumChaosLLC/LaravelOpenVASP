<?php

declare(strict_types=1);

use LaravelOpenVasp\Services\DatabaseTransferRepository;

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | All protocol routes are served under this prefix. Default:
    | /api/openvasp
    |
    */
    'route_prefix' => env('OPENVASP_ROUTE_PREFIX', 'api/openvasp'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Pluggable authentication/authorization middleware list.
    | Example: ['api', 'auth:sanctum'] or ['api', 'auth:api']
    |
    */
    'middleware' => ['api'],

    /*
    |--------------------------------------------------------------------------
    | Persistence Repository
    |--------------------------------------------------------------------------
    |
    | Swap this contract implementation to integrate custom persistence layer.
    |
    */
    'repository' => DatabaseTransferRepository::class,

    /*
    |--------------------------------------------------------------------------
    | Storage flags
    |--------------------------------------------------------------------------
    */
    'database' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Protocol metadata
    |--------------------------------------------------------------------------
    */
    'protocol' => [
        'version' => env('OPENVASP_PROTOCOL_VERSION', '1.0'),
        'network' => env('OPENVASP_NETWORK', 'openvasp-mainnet'),
    ],
];
