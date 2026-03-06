<?php

declare(strict_types=1);

namespace LaravelOpenVasp\IVMS;

final readonly class OriginatingVasp
{
    public function __construct(
        public LegalPerson $legalPerson,
        public array $attributes = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            legalPerson: LegalPerson::fromArray((array) ($data['legalPerson'] ?? [])),
            attributes: $data,
        );
    }

    public function toArray(): array
    {
        $attributes = $this->attributes;
        $attributes['legalPerson'] = $this->legalPerson->toArray();

        return $attributes;
    }
}
