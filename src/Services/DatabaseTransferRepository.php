<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Services;

use LaravelOpenVasp\Contracts\TransferRepository;
use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Models\OpenVaspTransfer;

class DatabaseTransferRepository implements TransferRepository
{
    public function create(array $payload): OpenVaspTransfer
    {
        return OpenVaspTransfer::query()->create([
            'message_id' => $payload['message_id'],
            'originator_lei' => $payload['originator_lei'],
            'beneficiary_lei' => $payload['beneficiary_lei'],
            'asset_symbol' => $payload['asset']['symbol'],
            'asset_amount' => $payload['asset']['amount'],
            'status' => TransferStatus::Pending->value,
            'payload' => $payload,
            'decision_reason' => null,
        ]);
    }

    public function findByMessageId(string $messageId): ?OpenVaspTransfer
    {
        return OpenVaspTransfer::query()->where('message_id', $messageId)->first();
    }

    public function updateStatus(OpenVaspTransfer $transfer, TransferStatus $status, ?array $reason = null): OpenVaspTransfer
    {
        $transfer->forceFill([
            'status' => $status->value,
            'decision_reason' => $reason,
        ])->save();

        return $transfer->refresh();
    }
}
