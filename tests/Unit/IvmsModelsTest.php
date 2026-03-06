<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Unit;

use LaravelOpenVasp\IVMS\Ivms101;
use LaravelOpenVasp\Tests\TestCase;

class IvmsModelsTest extends TestCase
{
    public function test_it_hydrates_and_serializes_ivms_models(): void
    {
        $payload = [
            'originator' => ['originatorPersons' => []],
            'beneficiary' => ['beneficiaryPersons' => []],
            'originatingVASP' => [
                'originatingVASP' => [
                    'legalPerson' => [
                        'nationalIdentification' => [
                            'nationalIdentifierType' => 'LEIX',
                            'nationalIdentifier' => '24IN00POZKARSTIN8350',
                        ],
                    ],
                ],
            ],
        ];

        $ivms = Ivms101::fromArray($payload);

        $this->assertTrue($ivms->originatingVasp->legalPerson->nationalIdentification->isValidLei());
        $this->assertSame($payload, $ivms->toArray());
    }
}
