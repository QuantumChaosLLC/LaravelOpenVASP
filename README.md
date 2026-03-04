# Laravel OpenVASP Package

A production-focused Laravel package that provides OpenVASP-compatible API primitives for Travel Rule messaging between VASPs.

> **Source of truth:** this package follows the OpenVASP association guidance and protocol concepts from [openvasp.org](https://www.openvasp.org), then maps them into Laravel-first HTTP + persistence workflows suitable for backend integration.

## Features

- API endpoints mounted under `/api/openvasp/*` by default.
- Transfer lifecycle endpoints:
  - create transfer message
  - retrieve transfer by message ID
  - accept / reject / settle / cancel state transitions
- Pluggable persistence via `TransferRepository` contract.
- Pluggable authentication middleware (Sanctum, Passport, custom guards, etc).
- Database-backed implementation with migration included.
- Testbench-based automated test suite.
- MIT licensed (no paid/commercial-use dependency required).

## Installation

```bash
composer require laravel-openvasp/laravel-openvasp
```

Publish config:

```bash
php artisan vendor:publish --tag=openvasp-config
```

Run migrations:

```bash
php artisan migrate
```

## Routes

By default, all routes are under `api/openvasp`:

- `GET /api/openvasp/health`
- `POST /api/openvasp/transfers`
- `GET /api/openvasp/transfers/{messageId}`
- `POST /api/openvasp/transfers/{messageId}/accept`
- `POST /api/openvasp/transfers/{messageId}/reject`
- `POST /api/openvasp/transfers/{messageId}/settle`
- `POST /api/openvasp/transfers/{messageId}/cancel`

## Configuration

`config/openvasp.php`

```php
return [
    'route_prefix' => env('OPENVASP_ROUTE_PREFIX', 'api/openvasp'),
    'middleware' => ['api'], // e.g. ['api', 'auth:sanctum']
    'repository' => \LaravelOpenVasp\Services\DatabaseTransferRepository::class,
    'database' => [
        'enabled' => true,
    ],
    'protocol' => [
        'version' => env('OPENVASP_PROTOCOL_VERSION', '1.0'),
        'network' => env('OPENVASP_NETWORK', 'openvasp-mainnet'),
    ],
];
```

### Authentication integration (pluggable)

Use whichever Laravel API auth stack you already run:

- Sanctum: `['api', 'auth:sanctum']`
- Passport/API guard: `['api', 'auth:api']`
- Custom middleware pipeline: `['api', 'my-custom-openvasp-auth']`

## Example transfer payload

```json
{
  "message_id": "msg-1000",
  "originator_lei": "24IN00POZKARSTIN8350",
  "beneficiary_lei": "529900T8BM49AURSDO55",
  "asset": {
    "symbol": "USDC",
    "amount": "1500.25"
  },
  "travel_rule": {
    "originator": {
      "name": "Alice Originator",
      "account_number": "ORIG-001"
    },
    "beneficiary": {
      "name": "Bob Beneficiary",
      "account_number": "BEN-001"
    },
    "originating_wallet": "0x1111111111111111111111111111111111111111",
    "beneficiary_wallet": "0x2222222222222222222222222222222222222222"
  }
}
```

## Extending persistence

Implement `LaravelOpenVasp\Contracts\TransferRepository` and set your class in config:

```php
'repository' => App\OpenVasp\CustomTransferRepository::class,
```

This allows integration with existing event stores, CQRS patterns, multi-tenant stores, or external protocol hubs.

## Continuous Integration

A GitHub Actions workflow is included at `.github/workflows/laravel-tests.yml` and runs the package test suite on pushes and pull requests against `main` and `feature/jetbox` using PHP 8.2 and 8.3.

## Testing

Run package tests:

```bash
composer test
```

## Production readiness checklist

- Add your auth middleware (`auth:sanctum`, `auth:api`, mTLS/API gateway checks).
- Restrict route access to trusted VASP peers.
- Add request signing / cryptographic verification middleware according to your OpenVASP interoperability policy.
- Enable detailed audit logging around state transitions.
- Configure retries/idempotency (message ID uniqueness is enforced in DB).
- Monitor the `/health` endpoint.

## License

MIT
