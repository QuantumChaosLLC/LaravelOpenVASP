<?php

declare(strict_types=1);

use LaravelOpenVasp\Services\DatabaseTransferRepository;

return [
    'route_prefix' => env('OPENVASP_ROUTE_PREFIX', 'api/openvasp'),

    'middleware' => ['api'],

    'repository' => DatabaseTransferRepository::class,

    'database' => [
        'enabled' => true,
    ],

    'protocol' => [
        'version' => env('OPENVASP_PROTOCOL_VERSION', '3.2.1'),
        'supported_extensions' => [],
    ],

    'identity' => [
        'name' => env('OPENVASP_IDENTITY_NAME', 'Example VASP Ltd.'),
        'lei' => env('OPENVASP_IDENTITY_LEI', '24IN00POZKARSTIN8350'),
        'x509' => env('OPENVASP_IDENTITY_X509', "-----BEGIN CERTIFICATE-----\nREPLACE_ME\n-----END CERTIFICATE-----\n"),
    ],
];
