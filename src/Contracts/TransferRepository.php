<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Contracts;

use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Models\OpenVaspTransfer;

interface TransferRepository
{
    public function create(array $payload): OpenVaspTransfer;

    public function findByMessageId(string $messageId): ?OpenVaspTransfer;

    public function updateStatus(OpenVaspTransfer $transfer, TransferStatus $status, ?array $reason = null): OpenVaspTransfer;
}
