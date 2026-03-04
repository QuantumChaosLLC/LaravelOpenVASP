<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Unit;

use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Services\DatabaseTransferRepository;
use LaravelOpenVasp\Tests\TestCase;

class DatabaseTransferRepositoryTest extends TestCase
{
    public function test_repository_creates_and_updates_transfer(): void
    {
        $repository = new DatabaseTransferRepository;

        $transfer = $repository->create([
            'message_id' => 'repo-1000',
            'originator_lei' => '24IN00POZKARSTIN8350',
            'beneficiary_lei' => '529900T8BM49AURSDO55',
            'asset' => ['symbol' => 'BTC', 'amount' => '0.50000000'],
            'travel_rule' => [
                'originator' => ['name' => 'Alice'],
                'beneficiary' => ['name' => 'Bob'],
                'originating_wallet' => 'orig-wallet',
                'beneficiary_wallet' => 'ben-wallet',
            ],
        ]);

        $this->assertSame(TransferStatus::Pending->value, $transfer->status);

        $updated = $repository->updateStatus($transfer, TransferStatus::Rejected, [
            'code' => 'SCREENING_FAIL',
            'message' => 'Sanctions screening failed',
        ]);

        $this->assertSame(TransferStatus::Rejected->value, $updated->status);
        $this->assertSame('SCREENING_FAIL', $updated->decision_reason['code']);
    }
}
