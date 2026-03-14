<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $trackingCode = $request->get('code');

        // No code yet — show the search form
        if (!$trackingCode) {
            return Inertia::render('Verify/Index');
        }

        $doc = DocumentRequest::where('tracking_code', $trackingCode)
            ->with('user:id,name')
            ->first();

        // Document not found in DB
        if (!$doc) {
            return Inertia::render('Verify/Result', [
                'status'       => 'not_found',
                'trackingCode' => $trackingCode,
            ]);
        }

        // Found but not yet anchored on blockchain
        if (!$doc->blockchain_tx_hash) {
            return Inertia::render('Verify/Result', [
                'status'       => 'not_anchored',
                'trackingCode' => $trackingCode,
                'documentType' => $doc->document_type,
                'department'   => $doc->department,
                'docStatus'    => $doc->status,
            ]);
        }

        // Verify against blockchain
        try {
            $service     = app(BlockchainService::class);
            $currentHash = $service->buildDocumentHash($doc);
            $chainResult = $service->verify($trackingCode, $currentHash);

            $status = match(true) {
                $chainResult['isRevoked']   => 'revoked',
                $chainResult['isAuthentic'] => 'authentic',
                default                     => 'tampered',
            };

        } catch (\Exception $e) {
            $status      = 'error';
            $chainResult = [];
            \Log::error('[Verify] Blockchain check failed', [
                'tracking_code' => $trackingCode,
                'error'         => $e->getMessage(),
            ]);
        }

        return Inertia::render('Verify/Result', [
            'status'       => $status,
            'trackingCode' => $trackingCode,
            'documentType' => $doc->document_type,
            'department'   => $doc->department,
            'issuedTo'     => $doc->user?->name,
            'anchoredAt'   => $doc->blockchain_anchored_at?->toISOString(),
            'txHash'       => $doc->blockchain_tx_hash,
            'network'      => $doc->blockchain_network,
            'explorerUrl'  => "https://amoy.polygonscan.com/tx/{$doc->blockchain_tx_hash}",
            'chainData'    => $chainResult ?? [],
        ]);
    }
}