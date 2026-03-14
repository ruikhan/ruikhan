<?php

namespace App\Services;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlockchainService
{
    private string $bridgeUrl;
    private string $apiSecret;

    public function __construct()
    {
        $this->bridgeUrl = rtrim(config('services.blockchain.bridge_url'), '/');
        $this->apiSecret = config('services.blockchain.api_secret');
    }

    /**
     * Build deterministic SHA-256 hash from document's immutable fields.
     * Field order is FIXED — never change or existing hashes break.
     */
    public function buildDocumentHash(DocumentRequest $doc): string
    {
        $payload = [
            'tracking_code' => $doc->tracking_code,
            'document_type' => $doc->document_type,
            'department'    => $doc->department,
            'user_id'       => $doc->user_id,
            'issued_at'     => $doc->updated_at?->toIso8601String(),
            'data_checksum' => hash('sha256', json_encode($doc->data ?? [])),
        ];

        return hash('sha256', json_encode($payload));
    }

    /**
     * Anchor document hash on Polygon via bridge.
     */
    public function anchor(DocumentRequest $doc, string $documentHash): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'    => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post("{$this->bridgeUrl}/anchor", [
                'trackingCode' => $doc->tracking_code,
                'documentHash' => $documentHash,
                'documentType' => $doc->document_type,
                'department'   => $doc->department,
                'issuedAt'     => now()->timestamp,
            ]);

            if ($response->failed()) {
                throw new \Exception('Bridge anchor failed: ' . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('[BlockchainService] Anchor failed', [
                'tracking_code' => $doc->tracking_code,
                'error'         => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify document hash against smart contract.
     */
    public function verify(string $trackingCode, string $claimedHash): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'    => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post("{$this->bridgeUrl}/verify", [
                'trackingCode' => $trackingCode,
                'claimedHash'  => $claimedHash,
            ]);

            if ($response->failed()) {
                throw new \Exception('Bridge verify failed: ' . $response->body());
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('[BlockchainService] Verify failed', [
                'tracking_code' => $trackingCode,
                'error'         => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}