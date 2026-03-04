<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Feature;

use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Tests\TestCase;

class OpenVaspRoutesTest extends TestCase
{
    public function test_health_endpoint_is_available(): void
    {
        $response = $this->getJson('/api/openvasp/health');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('protocol.name', 'openvasp');
    }

    public function test_it_creates_a_transfer_with_valid_payload(): void
    {
        $response = $this->postJson('/api/openvasp/transfers', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.message_id', 'msg-1000')
            ->assertJsonPath('data.originator_lei', '24IN00POZKARSTIN8350')
            ->assertJsonPath('data.status', TransferStatus::Pending->value);
    }

    public function test_it_requires_openvasp_fields_for_transfer_creation(): void
    {
        $response = $this->postJson('/api/openvasp/transfers', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'message_id',
                'originator_lei',
                'beneficiary_lei',
                'asset.symbol',
                'asset.amount',
                'travel_rule.originator',
                'travel_rule.beneficiary',
                'travel_rule.originating_wallet',
                'travel_rule.beneficiary_wallet',
            ]);
    }

    public function test_it_advances_transfer_lifecycle_through_protocol_endpoints(): void
    {
        $this->postJson('/api/openvasp/transfers', $this->validPayload())->assertCreated();

        $this->postJson('/api/openvasp/transfers/msg-1000/accept', [
            'reason' => ['code' => 'KYC_OK', 'message' => 'Beneficiary checks passed'],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', TransferStatus::Accepted->value);

        $this->postJson('/api/openvasp/transfers/msg-1000/settle')
            ->assertOk()
            ->assertJsonPath('data.status', TransferStatus::Settled->value);

        $this->getJson('/api/openvasp/transfers/msg-1000')
            ->assertOk()
            ->assertJsonPath('data.status', TransferStatus::Settled->value);
    }

    public function test_it_can_reject_and_cancel_transfer_with_reason(): void
    {
        $this->postJson('/api/openvasp/transfers', $this->validPayload())->assertCreated();

        $this->postJson('/api/openvasp/transfers/msg-1000/reject', [
            'reason' => ['code' => 'TRAVEL_RULE_INCOMPLETE', 'message' => 'Missing beneficiary data'],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', TransferStatus::Rejected->value)
            ->assertJsonPath('data.reason.code', 'TRAVEL_RULE_INCOMPLETE');

        $this->postJson('/api/openvasp/transfers/msg-1000/cancel', [
            'reason' => ['code' => 'ORIGINATOR_CANCELLED', 'message' => 'Originator aborted transfer'],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', TransferStatus::Cancelled->value);
    }

    private function validPayload(): array
    {
        return [
            'message_id' => 'msg-1000',
            'originator_lei' => '24IN00POZKARSTIN8350',
            'beneficiary_lei' => '529900T8BM49AURSDO55',
            'asset' => [
                'symbol' => 'USDC',
                'amount' => '1500.25',
            ],
            'travel_rule' => [
                'originator' => [
                    'name' => 'Alice Originator',
                    'account_number' => 'ORIG-001',
                ],
                'beneficiary' => [
                    'name' => 'Bob Beneficiary',
                    'account_number' => 'BEN-001',
                ],
                'originating_wallet' => '0x1111111111111111111111111111111111111111',
                'beneficiary_wallet' => '0x2222222222222222222222222222222222222222',
            ],
        ];
    }
}
