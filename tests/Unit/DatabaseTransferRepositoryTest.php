<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Unit;

use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Services\DatabaseTransferRepository;
use LaravelOpenVasp\Tests\TestCase;

class DatabaseTransferRepositoryTest extends TestCase
{
    public function test_repository_tracks_inquiry_resolution_and_confirmation(): void
    {
        $repository = new DatabaseTransferRepository;

        $transfer = $repository->createInquiry('inq-1000', [
            'amount' => 42,
            'asset' => ['symbol' => 'BTC'],
            'callback' => 'https://originator.example/inquiry-resolution',
            'IVMS101' => [
                'originator' => ['originatorPersons' => []],
                'beneficiary' => ['beneficiaryPersons' => []],
                'originatingVASP' => [
                    'originatingVASP' => [
                        'legalPerson' => [
                            'nationalIdentification' => [
                                'nationalIdentifier' => '24IN00POZKARSTIN8350',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(TransferStatus::InquiryReceived->value, $transfer->status);

        $approved = $repository->markApproved($transfer, [
            'address' => 'bc1qexampleaddress',
            'callback' => 'https://beneficiary.example/transfer-confirmation',
        ]);

        $this->assertSame(TransferStatus::Approved->value, $approved->status);
        $this->assertSame('bc1qexampleaddress', $approved->payment_address);

        $confirmed = $repository->markConfirmed($approved, '0xabc123', null);

        $this->assertSame(TransferStatus::Confirmed->value, $confirmed->status);
        $this->assertSame('0xabc123', $confirmed->txid);
    }
}
