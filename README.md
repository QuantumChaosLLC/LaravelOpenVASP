# Laravel OpenVASP (TRP)

Laravel package implementing a **minimum acceptable Travel Rule Protocol (TRP) API** aligned with the OpenVASP core specification.

- Spec source: https://gitlab.com/OpenVASP/travel-rule-protocol/-/blob/master/core/specification.md
- Interop server: https://api.trp.openvasp.org
- Interop guidance: https://gitlab.com/OpenVASP/interoperability

## What is implemented (minimum core)

Routes are mounted under `/api/openvasp` by default:

- `GET /api/openvasp/version`
- `GET /api/openvasp/identity`
- `POST /api/openvasp/inquiries/{inquiryId}`
- `POST /api/openvasp/inquiry-resolutions/{inquiryId}`
- `POST /api/openvasp/transfer-confirmations/{inquiryId}`

These map to the TRP core flow:
1. Transfer Inquiry
2. Transfer Inquiry Resolution
3. Transfer Confirmation

## Header behavior

For protocol endpoints, this package enforces:

- `api-version`
- `request-identifier` (UUID)

Responses echo both headers.

Optional `api-extensions` is checked against configured supported extensions. Unsupported extensions return `501 Not Implemented`.

## Installation

```bash
composer require laravel-openvasp/laravel-openvasp
php artisan vendor:publish --tag=openvasp-config
php artisan migrate
```

## Configuration

`config/openvasp.php`

- `route_prefix`: default `api/openvasp`
- `middleware`: pluggable auth stack (e.g. `auth:sanctum`, `auth:api`)
- `repository`: pluggable persistence implementation
- `protocol.version`: default `3.2.1`
- `protocol.supported_extensions`: default `[]`
- `identity.name`, `identity.lei`, `identity.x509`

Example LEI in config/tests: `24IN00POZKARSTIN8350`.

## Example inquiry payload

```json
{
  "amount": 150025,
  "callback": "https://originator.example/inquiry-resolution?q=4585839457",
  "asset": {
    "dti": "4H95J0R2X"
  },
  "IVMS101": {
    "originator": {"originatorPersons": []},
    "beneficiary": {"beneficiaryPersons": []},
    "originatingVASP": {
      "originatingVASP": {
        "legalPerson": {
          "nationalIdentification": {
            "nationalIdentifier": "24IN00POZKARSTIN8350"
          }
        }
      }
    }
  }
}
```

## IVMS models

The package now includes explicit IVMS value models under `LaravelOpenVasp\IVMS`:

- `Ivms101`
- `OriginatingVasp`
- `LegalPerson`
- `NationalIdentification`

`StoreTransferRequest::ivms101()` hydrates these models from validated payloads so inbound TRP inquiries are parsed into typed structures before persistence.

## Interoperability and reference libraries

This package aligns payload shape and LEI validation to help compatibility with tools/libraries referenced by OpenVASP interoperability docs:

- Rust IVMS: https://gitlab.com/21analytics/ivms101
- Rust LEI: https://gitlab.com/21analytics/lei
- Go LEI: https://github.com/trisacrypto/lei


## Compatibility review notes

The implementation and tests were reviewed against the interoperability resources and LEI/IVMS libraries listed by OpenVASP:

- IVMS model conventions from `21analytics/ivms101`
- LEI validation behavior from `21analytics/lei` and `trisacrypto/lei`
- OpenVASP interoperability server expectations from `OpenVASP/interoperability`

To improve cross-implementation compatibility, this package now:

- validates LEI values using ISO 17442 checksum (not only length/pattern)
- requires `nationalIdentifierType` as `LEIX` for originating VASP LEI in IVMS101
- validates DTI as a 9-character uppercase alphanumeric identifier
- includes optional live interop tests that verify `/version` and `/identity` behavior against `https://api.trp.openvasp.org`

## Tests

```bash
composer test
./vendor/bin/pint --test
```

Optional external interop test:

```bash
OPENVASP_RUN_INTEGRATION_TESTS=true composer test
```

## License

MIT
