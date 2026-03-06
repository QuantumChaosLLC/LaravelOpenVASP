<?php

declare(strict_types=1);

namespace LaravelOpenVasp\IVMS;

use LaravelOpenVasp\Support\Lei;

final readonly class NationalIdentification
{
    public function __construct(
        public string $nationalIdentifierType,
        public string $nationalIdentifier,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nationalIdentifierType: (string) ($data['nationalIdentifierType'] ?? ''),
            nationalIdentifier: (string) ($data['nationalIdentifier'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'nationalIdentifierType' => $this->nationalIdentifierType,
            'nationalIdentifier' => $this->nationalIdentifier,
        ];
    }

    public function isValidLei(): bool
    {
        return $this->nationalIdentifierType === 'LEIX' && Lei::isValid($this->nationalIdentifier);
    }
}
