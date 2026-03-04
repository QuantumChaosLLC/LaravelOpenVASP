<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason.code' => ['nullable', 'string', 'max:40'],
            'reason.message' => ['nullable', 'string', 'max:500'],
        ];
    }
}
