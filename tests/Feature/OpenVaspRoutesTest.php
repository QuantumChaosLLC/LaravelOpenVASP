<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Feature;

use Illuminate\Support\Str;
use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Tests\TestCase;

class OpenVaspRoutesTest extends TestCase
{
    public function test_version_endpoint_is_available(): void
    {
        $this->getJson('/api/openvasp/version')
            ->assertOk()
            ->assertJsonPath('version', '3.2.1');
    }

    public function test_identity_requires_protocol_headers(): void
    {
        $this->getJson('/api/openvasp/identity')->assertStatus(400);
    }

    public function test_identity_returns_trp_identity_with_header_echo(): void
    {
        $response = $this->withProtocolHeaders()->getJson('/api/openvasp/identity');

        $response->assertOk()
            ->assertHeader('api-version', '3.2.1')
            ->assertJsonPath('lei', '24IN00POZKARSTIN8350');
    }

    public function test_it_runs_minimum_trp_happy_path(): void
    {
        $this->withProtocolHeaders()->postJson('/api/openvasp/inquiries/inq-1000', $this->validInquiryPayload())
            ->assertOk()
            ->assertJsonPath('status', TransferStatus::InquiryReceived->value);

        $this->withProtocolHeaders()->postJson('/api/openvasp/inquiry-resolutions/inq-1000', [
            'approved' => [
                'address' => 'bc1qapprovedaddress',
                'callback' => 'https://beneficiary.example/transfer-confirmation?token=1',
            ],
        ])->assertNoContent();

        $this->withProtocolHeaders()->postJson('/api/openvasp/transfer-confirmations/inq-1000', [
            'txid' => '0x123456',
        ])->assertNoContent();
    }

    public function test_it_rejects_invalid_lei_checksum_in_ivms_payload(): void
    {
        $payload = $this->validInquiryPayload();
        $payload['IVMS101']['originatingVASP']['originatingVASP']['legalPerson']['nationalIdentification']['nationalIdentifier'] = '24IN00POZKARSTIN8351';

        $this->withProtocolHeaders()->postJson('/api/openvasp/inquiries/inq-1002', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'IVMS101.originatingVASP.originatingVASP.legalPerson.nationalIdentification.nationalIdentifier',
            ]);
    }

    public function test_it_rejects_unsupported_extensions(): void
    {
        $this->withHeaders([
            'api-version' => '3.2.1',
            'request-identifier' => (string) Str::uuid(),
            'api-extensions' => 'request-signing',
        ])->postJson('/api/openvasp/inquiries/inq-1001', $this->validInquiryPayload())
            ->assertStatus(501);
    }

    private function validInquiryPayload(): array
    {
        return [
            'amount' => 150025,
            'callback' => 'https://originator.example/inquiry-resolution?q=4585839457',
            'asset' => [
                'dti' => '4H95J0R2X',
            ],
            'IVMS101' => [
                'originator' => [
                    'originatorPersons' => [
                        ['naturalPerson' => ['name' => ['nameIdentifier' => [['primaryIdentifier' => 'Alice']]]]],
                    ],
                ],
                'beneficiary' => [
                    'beneficiaryPersons' => [
                        ['naturalPerson' => ['name' => ['nameIdentifier' => [['primaryIdentifier' => 'Bob']]]]],
                    ],
                ],
                'originatingVASP' => [
                    'originatingVASP' => [
                        'legalPerson' => [
                            'nationalIdentification' => [
                                'nationalIdentifierType' => 'LEIX',
                                'nationalIdentifier' => '24IN00POZKARSTIN8350',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function withProtocolHeaders(): self
    {
        return $this->withHeaders([
            'api-version' => '3.2.1',
            'request-identifier' => (string) Str::uuid(),
        ]);
    }
}
