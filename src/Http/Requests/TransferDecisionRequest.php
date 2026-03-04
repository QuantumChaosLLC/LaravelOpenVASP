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
            'approved' => ['nullable', 'array', 'required_without:rejected'],
            'approved.address' => ['required_with:approved', 'string', 'max:255'],
            'approved.callback' => ['required_with:approved', 'url', 'starts_with:https://'],
            'rejected' => ['nullable', 'string', 'max:1000', 'required_without:approved'],
        ];
    }
}
