<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Enums;

enum TransferStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Settled = 'settled';
    case Cancelled = 'cancelled';
}
