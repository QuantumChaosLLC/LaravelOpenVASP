<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferConfirmationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'txid' => ['nullable', 'string', 'max:255', 'required_without:canceled'],
            'canceled' => ['nullable', 'string', 'max:1000', 'required_without:txid'],
        ];
    }
}
