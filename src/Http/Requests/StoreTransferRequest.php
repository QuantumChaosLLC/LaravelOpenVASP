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
            'amount' => ['required', 'integer', 'min:1'],
            'callback' => ['required', 'url', 'starts_with:https://'],
            'asset' => ['required', 'array'],
            'asset.symbol' => ['nullable', 'string', 'max:32', 'required_without:asset.dti'],
            'asset.dti' => ['nullable', 'string', 'max:32', 'required_without:asset.symbol'],
            'IVMS101' => ['required', 'array'],
            'IVMS101.originator' => ['required', 'array'],
            'IVMS101.beneficiary' => ['required', 'array'],
            'IVMS101.originatingVASP.originatingVASP.legalPerson.nationalIdentification.nationalIdentifier' => ['required', 'string', 'regex:/^[A-Z0-9]{18}[0-9]{2}$/'],
        ];
    }
}
