<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use LaravelOpenVasp\Contracts\TransferRepository;
use LaravelOpenVasp\Http\Requests\StoreTransferRequest;
use LaravelOpenVasp\Http\Requests\TransferConfirmationRequest;
use LaravelOpenVasp\Http\Requests\TransferDecisionRequest;

class OpenVaspController extends Controller
{
    public function __construct(private readonly TransferRepository $repository) {}

    public function version(): JsonResponse
    {
        return response()->json(['version' => config('openvasp.protocol.version')]);
    }

    public function identity(Request $request): JsonResponse
    {
        $headers = $this->validateProtocolHeaders($request);

        return $this->withProtocolHeaders(response()->json([
            'name' => config('openvasp.identity.name'),
            'lei' => config('openvasp.identity.lei'),
            'x509' => config('openvasp.identity.x509'),
        ]), $headers['request_identifier']);
    }

    public function inquiry(string $inquiryId, StoreTransferRequest $request): JsonResponse
    {
        $headers = $this->validateProtocolHeaders($request);

        $payload = $request->validated();
        $payload['IVMS101'] = $request->ivms101()->toArray();

        $transfer = $this->repository->findByInquiryId($inquiryId)
            ?? $this->repository->createInquiry($inquiryId, $payload);

        return $this->withProtocolHeaders(response()->json([
            'version' => config('openvasp.protocol.version'),
            'status' => $transfer->status,
        ]), $headers['request_identifier']);
    }

    public function inquiryResolution(string $inquiryId, TransferDecisionRequest $request): JsonResponse
    {
        $headers = $this->validateProtocolHeaders($request);

        $transfer = $this->repository->findByInquiryId($inquiryId);
        abort_if($transfer === null, 404, 'Inquiry not found.');

        $validated = $request->validated();
        if (isset($validated['approved'])) {
            $this->repository->markApproved($transfer, $validated['approved']);
        } else {
            $this->repository->markRejected($transfer, $validated['rejected'] ?? null);
        }

        return $this->withProtocolHeaders(response()->json([], 204), $headers['request_identifier']);
    }

    public function transferConfirmation(string $inquiryId, TransferConfirmationRequest $request): JsonResponse
    {
        $headers = $this->validateProtocolHeaders($request);

        $transfer = $this->repository->findByInquiryId($inquiryId);
        abort_if($transfer === null, 404, 'Inquiry not found.');

        $validated = $request->validated();
        $this->repository->markConfirmed($transfer, $validated['txid'] ?? null, $validated['canceled'] ?? null);

        return $this->withProtocolHeaders(response()->json([], 204), $headers['request_identifier']);
    }

    private function validateProtocolHeaders(Request $request): array
    {
        $apiVersion = $request->header('api-version');
        $requestIdentifier = $request->header('request-identifier');

        abort_if(! is_string($apiVersion) || trim($apiVersion) === '', 400, 'Missing api-version header.');
        abort_if(! is_string($requestIdentifier) || ! Str::isUuid($requestIdentifier), 400, 'Invalid request-identifier header.');

        $requestedExtensions = collect(explode(',', (string) $request->header('api-extensions', '')))
            ->map(fn (string $value): string => trim($value))
            ->filter();

        $supportedExtensions = collect(config('openvasp.protocol.supported_extensions', []));
        $unsupported = $requestedExtensions->diff($supportedExtensions);

        abort_if($unsupported->isNotEmpty(), 501, 'Requested api-extensions are not supported.');

        return ['request_identifier' => $requestIdentifier];
    }

    private function withProtocolHeaders(JsonResponse $response, string $requestIdentifier): JsonResponse
    {
        return $response
            ->header('api-version', config('openvasp.protocol.version'))
            ->header('request-identifier', $requestIdentifier);
    }
}
