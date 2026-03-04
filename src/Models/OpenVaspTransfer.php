<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OpenVaspTransfer extends Model
{
    use HasUuids;

    protected $table = 'openvasp_transfers';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'decision_reason' => 'array',
        ];
    }
}
