<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Contracts;

use LaravelOpenVasp\Models\OpenVaspTransfer;

interface TransferRepository
{
    public function createInquiry(string $inquiryId, array $payload): OpenVaspTransfer;

    public function findByInquiryId(string $inquiryId): ?OpenVaspTransfer;

    public function markApproved(OpenVaspTransfer $transfer, array $approved): OpenVaspTransfer;

    public function markRejected(OpenVaspTransfer $transfer, ?string $reason): OpenVaspTransfer;

    public function markConfirmed(OpenVaspTransfer $transfer, ?string $txid, ?string $canceledReason): OpenVaspTransfer;
}
