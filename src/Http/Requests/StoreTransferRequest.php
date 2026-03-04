<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message_id' => ['required', 'string', 'max:64'],
            'originator_lei' => ['required', 'string', 'size:20'],
            'beneficiary_lei' => ['required', 'string', 'size:20'],
            'asset.symbol' => ['required', 'string', 'max:20'],
            'asset.amount' => ['required', 'numeric', 'gt:0'],
            'travel_rule.originator' => ['required', 'array'],
            'travel_rule.beneficiary' => ['required', 'array'],
            'travel_rule.originating_wallet' => ['required', 'string', 'max:255'],
            'travel_rule.beneficiary_wallet' => ['required', 'string', 'max:255'],
        ];
    }
}
