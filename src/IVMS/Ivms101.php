<?php

declare(strict_types=1);

namespace LaravelOpenVasp\IVMS;

final readonly class Ivms101
{
    public function __construct(
        public array $originator,
        public array $beneficiary,
        public OriginatingVasp $originatingVasp,
    ) {}

    public static function fromArray(array $data): self
    {
        $originatingVaspData = (array) (($data['originatingVASP'] ?? [])['originatingVASP'] ?? []);

        return new self(
            originator: (array) ($data['originator'] ?? []),
            beneficiary: (array) ($data['beneficiary'] ?? []),
            originatingVasp: OriginatingVasp::fromArray($originatingVaspData),
        );
    }

    public function toArray(): array
    {
        return [
            'originator' => $this->originator,
            'beneficiary' => $this->beneficiary,
            'originatingVASP' => [
                'originatingVASP' => $this->originatingVasp->toArray(),
            ],
        ];
    }
}
