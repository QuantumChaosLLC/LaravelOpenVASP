<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LaravelOpenVasp\IVMS\Ivms101;
use LaravelOpenVasp\Support\Lei;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function ivms101(): Ivms101
    {
        return Ivms101::fromArray((array) $this->validated('IVMS101', []));
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'callback' => ['required', 'url', 'starts_with:https://'],
            'asset' => ['required', 'array'],
            'asset.symbol' => ['nullable', 'string', 'max:32', 'required_without:asset.dti'],
            'asset.dti' => ['nullable', 'string', 'size:9', 'regex:/^[A-Z0-9]{9}$/', 'required_without:asset.symbol'],
            'IVMS101' => ['required', 'array'],
            'IVMS101.originator' => ['required', 'array'],
            'IVMS101.beneficiary' => ['required', 'array'],
            'IVMS101.originatingVASP.originatingVASP.legalPerson.nationalIdentification.nationalIdentifierType' => ['required', 'string', 'in:LEIX'],
            'IVMS101.originatingVASP.originatingVASP.legalPerson.nationalIdentification.nationalIdentifier' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! Lei::isValid($value)) {
                        $fail('The '.$attribute.' must be a valid LEI (ISO 17442 checksum).');
                    }
                },
            ],
        ];
    }
}
