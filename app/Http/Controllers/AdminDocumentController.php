<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Jobs\AnchorDocumentJob;
use App\Services\BlockchainService;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    /**
     * Display all document requests
     * ✅ FIX: ->through() instead of ->map() on paginator
     */

// In AdminDocumentController::index()
// ✅ FIX: paginate() returns LengthAwarePaginator — Vue reads .data, .links, .meta
// Replace the current index() method with this:

public function index()
{
    try {
        $requests = DocumentRequest::with('user:id,name,email')
            ->latest()
            ->paginate(15)
            ->through(function ($request) {
                return [
                    'id'                      => $request->id,
                    'tracking_code'           => $request->tracking_code ?? 'N/A',
                    'user' => [
                        'name'  => $request->user?->name ?? 'Unknown User',
                        'email' => $request->user?->email ?? 'No Email',
                    ],
                    'document_type'           => $request->document_type ?? 'N/A',
                    'department'              => $request->department ?? 'N/A',
                    'status'                  => $request->status ?? 'pending',
                    'blockchain_status'       => $request->blockchain_status,
                    'blockchain_tx_hash'      => $request->blockchain_tx_hash,
                    'blockchain_explorer_url' => $request->blockchain_explorer_url,
                    'created_at'              => $request->created_at?->toISOString(),
                    'updated_at'              => $request->updated_at?->toISOString(),
                ];
            });

        return Inertia::render('Admin/Documents/Index', [
            // ✅ Inertia automatically serializes LengthAwarePaginator to
            // { data: [...], links: { first, last, prev, next }, meta: { current_page, last_page, total, ... } }
            'requests' => $requests,
        ]);

    } catch (\Exception $e) {
        Log::error('Admin Documents Index Error: ' . $e->getMessage());

        return Inertia::render('Admin/Documents/Index', [
            'requests' => [ 'data' => [], 'links' => [], 'meta' => ['total' => 0, 'current_page' => 1, 'last_page' => 1] ],
        ]);
    }
}

    /**
     * Display a specific document request
     */
    public function show($id)
    {
        try {
            $documentRequest = DocumentRequest::with('user:id,name,email,phone_number,address')
                ->findOrFail($id);

            if (!$documentRequest->user) {
                Log::warning('Document has no associated user', ['document_id' => $id]);
                return redirect()->route('admin.documents.index')
                    ->withErrors(['error' => 'This document has no associated user']);
            }

            // ✅ FIX: attachments is already cast as array in model
            $attachments = $documentRequest->attachments ?? [];

            return Inertia::render('Admin/Documents/Show', [
                'docRequest' => [
                    'id'                      => $documentRequest->id,
                    'tracking_code'           => $documentRequest->tracking_code ?? 'N/A',
                    'document_type'           => $documentRequest->document_type ?? 'N/A',
                    'department'              => $documentRequest->department ?? 'N/A',
                    'status'                  => $documentRequest->status ?? 'pending',
                    'data'                    => $documentRequest->data ?? [],
                    'attachments'             => $attachments,
                    'user_remarks'            => $documentRequest->user_remarks ?? null,
                    'admin_remarks'           => $documentRequest->admin_note ?? '',
                    'appointment_date'        => $documentRequest->appointment_date ?? null,
                    'icon'                    => $this->getDepartmentIcon($documentRequest->department),
                    'created_at'              => $documentRequest->created_at?->toISOString() ?? null,
                    'updated_at'              => $documentRequest->updated_at?->toISOString() ?? null,
                    'blockchain_status'       => $documentRequest->blockchain_status,
                    'blockchain_tx_hash'      => $documentRequest->blockchain_tx_hash,
                    'blockchain_anchored_at'  => $documentRequest->blockchain_anchored_at?->toISOString(),
                    'blockchain_network'      => $documentRequest->blockchain_network,
                    'blockchain_explorer_url' => $documentRequest->blockchain_explorer_url,
                ],
                'user' => [
                    'id'           => $documentRequest->user->id,
                    'name'         => $documentRequest->user->name ?? 'Unknown User',
                    'email'        => $documentRequest->user->email ?? 'No Email',
                    'phone_number' => $documentRequest->user->phone_number ?? null,
                    'address'      => $documentRequest->user->address ?? null,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Document not found: ' . $id);
            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Document not found']);

        } catch (\Exception $e) {
            Log::error('Admin Document Show Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Failed to load document details: ' . $e->getMessage()]);
        }
    }

    /**
     * Update document status with workflow tracking
     * ✅ FIX: Old status captured BEFORE update, workflow_history uses array cast
     */
    public function update(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'status'           => 'required|in:pending,processing,ready_for_pickup,completed,rejected',
                'admin_remarks'    => 'nullable|string|max:1000',
                'appointment_date' => 'nullable|date',
                'admin_signature'  => 'nullable|string',
            ]);

            if ($validated['status'] === 'ready_for_pickup' && empty($validated['appointment_date'])) {
                return redirect()->back()
                    ->withErrors(['appointment_date' => 'Appointment date is required when status is Ready for Pickup']);
            }

            // ✅ Capture OLD status BEFORE update
            $oldStatus = $documentRequest->status;

            // ✅ FIX: workflow_history is cast as array — no json_decode needed
            $workflowHistory   = $documentRequest->workflow_history ?? [];
            $workflowHistory[] = [
                'status'           => $validated['status'],
                'admin_id'         => auth()->id(),
                'admin_name'       => auth()->user()->name,
                'timestamp'        => now()->toIso8601String(),
                'note'             => $validated['admin_remarks'] ?? null,
                'appointment_date' => $validated['appointment_date'] ?? null,
            ];

            $updateData = [
                'status'           => $validated['status'],
                'admin_note'       => $validated['admin_remarks'] ?? null,
                'appointment_date' => $validated['appointment_date'] ?? null,
                'workflow_history' => $workflowHistory, // ✅ no json_encode needed
            ];

            if (!empty($validated['admin_signature']) && $validated['status'] === 'completed') {
                $updateData['admin_signature']      = $validated['admin_signature'];
                $updateData['admin_signature_date'] = now();
            }

            $documentRequest->update($updateData);

            // ✅ Notify only if status actually changed
            if ($oldStatus !== $validated['status'] && $documentRequest->user) {
                $documentRequest->user->notify(
                    new DocumentStatusUpdated($documentRequest, $oldStatus)
                );
            }

            return redirect()->back()
                ->with('success', "Document status updated to '{$validated['status']}' successfully!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['error' => 'Document not found']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('Admin Document Update Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update document. Please try again.']);
        }
    }

    /**
     * Approve document with digital signature
     * ✅ Notifies user + anchors on Polygon blockchain
     */
    public function approve(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'admin_signature' => 'required|string',
                'admin_note'      => 'nullable|string|max:1000',
            ]);

            // ✅ Capture old status before update
            $oldStatus = $documentRequest->status;

            // ✅ FIX: no json_decode needed
            $workflowHistory   = $documentRequest->workflow_history ?? [];
            $workflowHistory[] = [
                'status'     => 'approved',
                'admin_id'   => auth()->id(),
                'admin_name' => auth()->user()->name,
                'timestamp'  => now()->toIso8601String(),
                'note'       => $validated['admin_note'] ?? 'Document approved',
            ];

            $documentRequest->update([
                'status'               => 'completed',
                'admin_signature'      => $validated['admin_signature'],
                'admin_signature_date' => now(),
                'admin_note'           => $validated['admin_note'] ?? null,
                'workflow_history'     => $workflowHistory, // ✅ no json_encode
            ]);

            // ✅ Notify user document is completed
            if ($documentRequest->user) {
                $documentRequest->user->notify(
                    new DocumentStatusUpdated($documentRequest, $oldStatus)
                );
            }

            // ✅ Blockchain — anchor approved document on Polygon
            $documentHash = app(BlockchainService::class)->buildDocumentHash($documentRequest);
            AnchorDocumentJob::dispatch($documentRequest, $documentHash);
            Log::info('[Approve] Blockchain anchor dispatched', [
                'tracking_code' => $documentRequest->tracking_code
            ]);

            return redirect()->back()->with('success', 'Document approved successfully');

        } catch (\Exception $e) {
            Log::error('Document Approval Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to approve document']);
        }
    }

    /**
     * Reject document with reason
     * ✅ Notifies user of rejection
     */
    public function reject(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            // ✅ Capture old status before update
            $oldStatus = $documentRequest->status;

            // ✅ FIX: no json_decode needed
            $workflowHistory   = $documentRequest->workflow_history ?? [];
            $workflowHistory[] = [
                'status'     => 'rejected',
                'admin_id'   => auth()->id(),
                'admin_name' => auth()->user()->name,
                'timestamp'  => now()->toIso8601String(),
                'reason'     => $validated['rejection_reason'],
            ];

            $documentRequest->update([
                'status'           => 'rejected',
                'admin_note'       => $validated['rejection_reason'],
                'workflow_history' => $workflowHistory, // ✅ no json_encode
            ]);

            // ✅ Notify user document is rejected
            if ($documentRequest->user) {
                $documentRequest->user->notify(
                    new DocumentStatusUpdated($documentRequest, $oldStatus)
                );
            }

            return redirect()->back()->with('success', 'Document rejected successfully');

        } catch (\Exception $e) {
            Log::error('Document Rejection Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to reject document']);
        }
    }

    /**
     * Download attachment
     * ✅ FIX: attachments is already cast as array — no json_decode needed
     */
    public function downloadAttachment(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate(['path' => 'required|string']);
            $filePath  = $validated['path'];

            // ✅ FIX: already an array from model cast
            $attachments = $documentRequest->attachments ?? [];

            if (!in_array($filePath, $attachments)) {
                return redirect()->back()->withErrors(['error' => 'File not found']);
            }

            if (!Storage::exists($filePath)) {
                return redirect()->back()->withErrors(['error' => 'File not found on server']);
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            Log::error('Download Attachment Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to download file']);
        }
    }

    public function print($id)
{
    try {
        $documentRequest = DocumentRequest::with('user:id,name,email,phone_number,address')
            ->findOrFail($id);

        // Only completed documents can be printed
        if ($documentRequest->status !== 'completed') {
            abort(403, 'Document must be completed before printing.');
        }

        return Inertia::render('Admin/Documents/Print', [
            'docRequest' => [
                'id'                      => $documentRequest->id,
                'tracking_code'           => $documentRequest->tracking_code,
                'document_type'           => $documentRequest->document_type,
                'department'              => $documentRequest->department,
                'status'                  => $documentRequest->status,
                'data'                    => $documentRequest->data ?? [],
                'admin_signature'         => $documentRequest->admin_signature,
                'admin_signature_date'    => $documentRequest->admin_signature_date?->toISOString(),
                'blockchain_tx_hash'      => $documentRequest->blockchain_tx_hash,
                'blockchain_network'      => $documentRequest->blockchain_network,
                'blockchain_explorer_url' => $documentRequest->blockchain_explorer_url,
                'blockchain_anchored_at'  => $documentRequest->blockchain_anchored_at?->toISOString(),
                'blockchain_status'       => $documentRequest->blockchain_status,
                'created_at'              => $documentRequest->created_at?->toISOString(),
                'updated_at'              => $documentRequest->updated_at?->toISOString(),
            ],
            'user' => [
                'id'           => $documentRequest->user->id,
                'name'         => $documentRequest->user->name,
                'email'        => $documentRequest->user->email,
                'phone_number' => $documentRequest->user->phone_number,
                'address'      => $documentRequest->user->address,
            ],
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'Document not found.');
    } catch (\Exception $e) {
        Log::error('Document Print Error: ' . $e->getMessage(), ['id' => $id]);
        abort(500, 'Failed to load document for printing.');
    }
}

    private function getFileType($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'txt'  => 'text/plain',
        ];
        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    private function getDepartmentIcon($department)
    {
        $icons = [
            'Municipal Civil Registrar' => '📜',
            'Municipal Health Office'   => '🩺',
            'Business Permits (BPLO)'   => '💼',
            'Engineering Office'        => '🏗️',
            'Agriculture Office'        => '🌾',
            'Social Welfare (MSWDO)'    => '🤝',
            'Treasurer\'s Office'       => '💰',
            'Assessor\'s Office'        => '🏡',
            'MPDO'                      => '🗺️',
            'MDRRMO'                    => '🚨',
            'Barangay Certifications'   => '🏘️',
        ];
        return $icons[$department] ?? '📄';
    }
}