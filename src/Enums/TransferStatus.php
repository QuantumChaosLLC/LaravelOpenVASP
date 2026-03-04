<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Enums;

enum TransferStatus: string
{
    case InquiryReceived = 'inquiry_received';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
}
