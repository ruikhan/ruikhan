<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminDocumentController extends Controller
{
    /**
     * Display all document requests
     */
    public function index()
    {
        try {
            $requests = DocumentRequest::with('user:id,name,email,phone_number,address')
                ->latest()
                ->paginate(15)
                ->map(function ($request) {
                    return [
                        'id'             => $request->id,
                        'tracking_code'  => $request->tracking_code ?? 'N/A',
                        'user'           => [
                            'name'  => $request->user?->name  ?? 'Unknown User',
                            'email' => $request->user?->email ?? 'No Email',
                        ],
                        'document_type' => $request->document_type ?? 'N/A',
                        'department'    => $request->department   ?? 'N/A',
                        'status'        => $request->status       ?? 'pending',
                        'created_at'    => $request->created_at?->toISOString() ?? null,
                        'updated_at'    => $request->updated_at?->toISOString() ?? null,
                    ];
                });

            return Inertia::render('Admin/Documents/Index', [
                'requests' => $requests,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Documents Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Admin/Documents/Index', [
                'requests' => [],
            ])->with('error', 'Failed to load documents. Please refresh the page.');
        }
    }

    /**
     * Display a specific document request with all details.
     *
     * ✅ FIX 2: appointment_date is now formatted as "Y-m-d\TH:i"
     *           so <input type="datetime-local"> stops throwing the
     *           "does not conform to required format" browser warning.
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

            // Format attachments
            $attachments = [];
            if ($documentRequest->attachments) {
                if (is_string($documentRequest->attachments)) {
                    $attachments = json_decode($documentRequest->attachments, true) ?? [];
                } elseif (is_array($documentRequest->attachments)) {
                    $attachments = $documentRequest->attachments;
                }
            }

            // ✅ FIX 2: Convert "2026-03-20T10:00:00.000000Z" → "2026-03-20T10:00"
            //    HTML datetime-local inputs require exactly "yyyy-MM-ddThh:mm"
            $appointmentDate = null;
            if ($documentRequest->appointment_date) {
                try {
                    $appointmentDate = \Carbon\Carbon::parse($documentRequest->appointment_date)
                        ->format('Y-m-d\TH:i');
                } catch (\Exception $e) {
                    $appointmentDate = null;
                }
            }

            return Inertia::render('Admin/Documents/Show', [
                'docRequest' => [
                    'id'               => $documentRequest->id,
                    'tracking_code'    => $documentRequest->tracking_code ?? 'N/A',
                    'document_type'    => $documentRequest->document_type ?? 'N/A',
                    'department'       => $documentRequest->department    ?? 'N/A',
                    'status'           => $documentRequest->status        ?? 'pending',
                    'data'             => $documentRequest->data          ?? [],
                    'attachments'      => $attachments,
                    'user_remarks'     => $documentRequest->user_remarks  ?? null,
                    'admin_remarks'    => $documentRequest->admin_note    ?? '',
                    'appointment_date' => $appointmentDate,  // ✅ clean format
                    'icon'             => $this->getDepartmentIcon($documentRequest->department),
                    'created_at'       => $documentRequest->created_at?->toISOString() ?? null,
                    'updated_at'       => $documentRequest->updated_at?->toISOString() ?? null,
                    'workflow_history' => $documentRequest->workflow_history
                        ? json_decode($documentRequest->workflow_history, true)
                        : [],
                    'admin_signature'  => $documentRequest->admin_signature      ?? null,
                    'admin_signature_date' => $documentRequest->admin_signature_date
                        ? \Carbon\Carbon::parse($documentRequest->admin_signature_date)->toISOString()
                        : null,
                ],
                'user' => [
                    'id'           => $documentRequest->user->id,
                    'name'         => $documentRequest->user->name         ?? 'Unknown User',
                    'email'        => $documentRequest->user->email        ?? 'No Email',
                    'phone_number' => $documentRequest->user->phone_number ?? null,
                    'address'      => $documentRequest->user->address      ?? null,
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Document not found: ' . $id);
            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Document not found']);

        } catch (\Exception $e) {
            Log::error('Admin Document Show Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Failed to load document details: ' . $e->getMessage()]);
        }
    }

    /**
     * ✅ FIX 1: Print/preview method — was completely missing.
     *    Route: GET /admin/documents/{id}/print  (admin.documents.print)
     *
     *    Returns a print-optimised full-page view of the document.
     *    The Vue component can open this in a new tab and call window.print().
     */
    public function print($id)
    {
        try {
            $documentRequest = DocumentRequest::with('user:id,name,email,phone_number,address')
                ->findOrFail($id);

            $attachments = [];
            if ($documentRequest->attachments) {
                if (is_string($documentRequest->attachments)) {
                    $attachments = json_decode($documentRequest->attachments, true) ?? [];
                } elseif (is_array($documentRequest->attachments)) {
                    $attachments = $documentRequest->attachments;
                }
            }

            $appointmentDate = null;
            if ($documentRequest->appointment_date) {
                try {
                    $appointmentDate = \Carbon\Carbon::parse($documentRequest->appointment_date)
                        ->format('F d, Y h:i A');
                } catch (\Exception $e) {
                    $appointmentDate = null;
                }
            }

            return Inertia::render('Admin/Documents/Print', [
                'docRequest' => [
                    'id'               => $documentRequest->id,
                    'tracking_code'    => $documentRequest->tracking_code ?? 'N/A',
                    'document_type'    => $documentRequest->document_type ?? 'N/A',
                    'department'       => $documentRequest->department    ?? 'N/A',
                    'status'           => $documentRequest->status        ?? 'pending',
                    'data'             => $documentRequest->data          ?? [],
                    'attachments'      => $attachments,
                    'user_remarks'     => $documentRequest->user_remarks  ?? null,
                    'admin_remarks'    => $documentRequest->admin_note    ?? null,
                    'appointment_date' => $appointmentDate,
                    'icon'             => $this->getDepartmentIcon($documentRequest->department),
                    'created_at'       => $documentRequest->created_at?->format('F d, Y h:i A') ?? null,
                    'admin_signature'  => $documentRequest->admin_signature ?? null,
                    'admin_signature_date' => $documentRequest->admin_signature_date
                        ? \Carbon\Carbon::parse($documentRequest->admin_signature_date)->format('F d, Y')
                        : null,
                    'workflow_history' => $documentRequest->workflow_history
                        ? json_decode($documentRequest->workflow_history, true)
                        : [],
                ],
                'user' => [
                    'name'         => $documentRequest->user->name         ?? 'Unknown',
                    'email'        => $documentRequest->user->email        ?? '',
                    'phone_number' => $documentRequest->user->phone_number ?? '',
                    'address'      => $documentRequest->user->address      ?? '',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Document not found']);

        } catch (\Exception $e) {
            Log::error('Admin Document Print Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Failed to load document for printing.']);
        }
    }

    /**
     * Update document status with workflow tracking
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

            $workflowHistory = $documentRequest->workflow_history
                ? json_decode($documentRequest->workflow_history, true)
                : [];

            $workflowHistory[] = [
                'status'           => $validated['status'],
                'admin_id'         => auth()->id(),
                'admin_name'       => auth()->user()->name,
                'timestamp'        => now()->toIso8601String(),
                'note'             => $validated['admin_remarks']    ?? null,
                'appointment_date' => $validated['appointment_date'] ?? null,
            ];

            $updateData = [
                'status'           => $validated['status'],
                'admin_note'       => $validated['admin_remarks']    ?? null,
                'appointment_date' => $validated['appointment_date'] ?? null,
                'workflow_history' => json_encode($workflowHistory),
            ];

            if (!empty($validated['admin_signature']) && $validated['status'] === 'completed') {
                $updateData['admin_signature']      = $validated['admin_signature'];
                $updateData['admin_signature_date'] = now();
            }

            $documentRequest->update($updateData);

            return redirect()->back()
                ->with('success', "Document status updated to '{$validated['status']}' successfully!");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['error' => 'Document not found']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('Admin Document Update Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to update document. Please try again.']);
        }
    }

    /**
     * Approve document with signature
     */
    public function approve(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'admin_signature' => 'required|string',
                'admin_note'      => 'nullable|string|max:1000',
            ]);

            $workflowHistory = $documentRequest->workflow_history
                ? json_decode($documentRequest->workflow_history, true)
                : [];

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
                'workflow_history'     => json_encode($workflowHistory),
            ]);

            return redirect()->back()->with('success', 'Document approved successfully');

        } catch (\Exception $e) {
            Log::error('Document Approval Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to approve document']);
        }
    }

    /**
     * Reject document
     */
    public function reject(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $workflowHistory = $documentRequest->workflow_history
                ? json_decode($documentRequest->workflow_history, true)
                : [];

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
                'workflow_history' => json_encode($workflowHistory),
            ]);

            return redirect()->back()->with('success', 'Document rejected successfully');

        } catch (\Exception $e) {
            Log::error('Document Rejection Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to reject document']);
        }
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'path' => 'required|string',
            ]);

            $filePath   = $validated['path'];
            $attachments = $documentRequest->attachments
                ? json_decode($documentRequest->attachments, true)
                : [];

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
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to download file']);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function getDepartmentIcon($department): string
    {
        return [
            'Municipal Civil Registrar'   => '📜',
            'Municipal Health Office'     => '🩺',
            'Business Permits (BPLO)'     => '💼',
            'Engineering Office'          => '🏗️',
            'Agriculture Office'          => '🌾',
            'Social Welfare (MSWDO)'      => '🤝',
            'Treasurer\'s Office'         => '💰',
            'Assessor\'s Office'          => '🏡',
            'MPDO'                        => '🗺️',
            'MDRRMO'                      => '🚨',
            'Barangay Certifications'     => '🏘️',
        ][$department] ?? '📄';
    }
}