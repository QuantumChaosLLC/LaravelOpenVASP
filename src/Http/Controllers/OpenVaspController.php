<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use LaravelOpenVasp\Contracts\TransferRepository;
use LaravelOpenVasp\Enums\TransferStatus;
use LaravelOpenVasp\Http\Requests\StoreTransferRequest;
use LaravelOpenVasp\Http\Requests\TransferDecisionRequest;

class OpenVaspController extends Controller
{
    public function __construct(private readonly TransferRepository $repository) {}

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'protocol' => [
                'name' => 'openvasp',
                'version' => config('openvasp.protocol.version'),
                'network' => config('openvasp.protocol.network'),
            ],
        ]);
    }

    public function store(StoreTransferRequest $request): JsonResponse
    {
        $transfer = $this->repository->create($request->validated());

        return response()->json([
            'data' => $this->transformTransfer($transfer),
        ], 201);
    }

    public function show(string $messageId): JsonResponse
    {
        $transfer = $this->repository->findByMessageId($messageId);

        abort_if($transfer === null, 404, 'Transfer not found.');

        return response()->json(['data' => $this->transformTransfer($transfer)]);
    }

    public function accept(string $messageId, TransferDecisionRequest $request): JsonResponse
    {
        $transfer = $this->repository->findByMessageId($messageId);
        abort_if($transfer === null, 404, 'Transfer not found.');

        $transfer = $this->repository->updateStatus($transfer, TransferStatus::Accepted, $request->validated('reason'));

        return response()->json(['data' => $this->transformTransfer($transfer)]);
    }

    public function reject(string $messageId, TransferDecisionRequest $request): JsonResponse
    {
        $transfer = $this->repository->findByMessageId($messageId);
        abort_if($transfer === null, 404, 'Transfer not found.');

        $transfer = $this->repository->updateStatus($transfer, TransferStatus::Rejected, $request->validated('reason'));

        return response()->json(['data' => $this->transformTransfer($transfer)]);
    }

    public function settle(string $messageId): JsonResponse
    {
        $transfer = $this->repository->findByMessageId($messageId);
        abort_if($transfer === null, 404, 'Transfer not found.');

        $transfer = $this->repository->updateStatus($transfer, TransferStatus::Settled);

        return response()->json(['data' => $this->transformTransfer($transfer)]);
    }

    public function cancel(string $messageId, TransferDecisionRequest $request): JsonResponse
    {
        $transfer = $this->repository->findByMessageId($messageId);
        abort_if($transfer === null, 404, 'Transfer not found.');

        $transfer = $this->repository->updateStatus($transfer, TransferStatus::Cancelled, $request->validated('reason'));

        return response()->json(['data' => $this->transformTransfer($transfer)]);
    }

    private function transformTransfer($transfer): array
    {
        return [
            'message_id' => $transfer->message_id,
            'originator_lei' => $transfer->originator_lei,
            'beneficiary_lei' => $transfer->beneficiary_lei,
            'asset' => [
                'symbol' => $transfer->asset_symbol,
                'amount' => $transfer->asset_amount,
            ],
            'status' => $transfer->status,
            'reason' => $transfer->decision_reason,
            'payload' => $transfer->payload,
            'created_at' => optional($transfer->created_at)?->toAtomString(),
            'updated_at' => optional($transfer->updated_at)?->toAtomString(),
        ];
    }
}
