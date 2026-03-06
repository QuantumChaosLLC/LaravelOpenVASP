<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Services;

use LaravelOpenVasp\Contracts\TransferRepository;
use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Models\OpenVaspTransfer;

class DatabaseTransferRepository implements TransferRepository
{
    public function createInquiry(string $inquiryId, array $payload): OpenVaspTransfer
    {
        return OpenVaspTransfer::query()->create([
            'inquiry_id' => $inquiryId,
            'status' => TransferStatus::InquiryReceived->value,
            'inquiry_payload' => $payload,
        ]);
    }

    public function findByInquiryId(string $inquiryId): ?OpenVaspTransfer
    {
        return OpenVaspTransfer::query()->where('inquiry_id', $inquiryId)->first();
    }

    public function markApproved(OpenVaspTransfer $transfer, array $approved): OpenVaspTransfer
    {
        $transfer->forceFill([
            'status' => TransferStatus::Approved->value,
            'resolution_payload' => ['approved' => $approved],
            'payment_address' => $approved['address'],
            'rejection_reason' => null,
        ])->save();

        return $transfer->refresh();
    }

    public function markRejected(OpenVaspTransfer $transfer, ?string $reason): OpenVaspTransfer
    {
        $transfer->forceFill([
            'status' => TransferStatus::Rejected->value,
            'resolution_payload' => ['rejected' => $reason],
            'payment_address' => null,
            'rejection_reason' => $reason,
        ])->save();

        return $transfer->refresh();
    }

    public function markConfirmed(OpenVaspTransfer $transfer, ?string $txid, ?string $canceledReason): OpenVaspTransfer
    {
        $status = $txid !== null ? TransferStatus::Confirmed : TransferStatus::Canceled;

        $transfer->forceFill([
            'status' => $status->value,
            'confirmation_payload' => [
                'txid' => $txid,
                'canceled' => $canceledReason,
            ],
            'txid' => $txid,
            'canceled_reason' => $canceledReason,
        ])->save();

        return $transfer->refresh();
    }
}
