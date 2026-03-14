<?php

namespace App\Jobs;

use App\Models\DocumentRequest;
use App\Services\BlockchainService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnchorDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;
    public int $timeout = 120;

    public function __construct(
        private DocumentRequest $document,
        private string $documentHash
    ) {}

    public function handle(BlockchainService $service): void
    {
        Log::info('[AnchorJob] Starting', [
            'tracking_code' => $this->document->tracking_code,
        ]);

        $this->document->update(['blockchain_status' => 'anchoring']);

        $result = $service->anchor($this->document, $this->documentHash);

        if (empty($result['txHash'])) {
            $this->document->update(['blockchain_status' => 'failed']);
            throw new \Exception('No txHash returned — retrying');
        }

        $this->document->update([
            'blockchain_tx_hash'       => $result['txHash'],
            'blockchain_document_hash' => $this->documentHash,
            'blockchain_anchored_at'   => now(),
            'blockchain_network'       => $result['network'],
            'blockchain_status'        => 'anchored',
        ]);

        Log::info('[AnchorJob] Anchored successfully', [
            'tracking_code' => $this->document->tracking_code,
            'tx_hash'       => $result['txHash'],
            'block'         => $result['blockNumber'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[AnchorJob] All retries exhausted', [
            'tracking_code' => $this->document->tracking_code,
            'error'         => $exception->getMessage(),
        ]);

        $this->document->update(['blockchain_status' => 'failed']);
    }
}