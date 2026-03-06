<?php

declare(strict_types=1);

namespace LaravelOpenVasp\IVMS;

final readonly class LegalPerson
{
    public function __construct(
        public NationalIdentification $nationalIdentification,
        public array $attributes = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nationalIdentification: NationalIdentification::fromArray((array) ($data['nationalIdentification'] ?? [])),
            attributes: $data,
        );
    }

    public function toArray(): array
    {
        $attributes = $this->attributes;
        $attributes['nationalIdentification'] = $this->nationalIdentification->toArray();

        return $attributes;
    }
}
