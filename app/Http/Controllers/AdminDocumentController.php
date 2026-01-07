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
            $requests = DocumentRequest::with('user:id,name,email')
                ->latest()
                ->paginate(15)
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => [
                            'name' => $request->user?->name ?? 'Unknown User',
                            'email' => $request->user?->email ?? 'No Email',
                        ],
                        'document_type' => $request->document_type ?? 'N/A',
                        'purpose' => $request->purpose ?? 'No purpose specified',
                        'status' => $request->status ?? 'pending',
                        'created_at' => $request->created_at?->toISOString() ?? null,
                        'updated_at' => $request->updated_at?->toISOString() ?? null,
                    ];
                });

            return Inertia::render('Admin/Documents/Index', [
                'requests' => $requests,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Documents Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Admin/Documents/Index', [
                'requests' => [],
            ])->with('error', 'Failed to load documents. Please refresh the page.');
        }
    }

    /**
     * Display a specific document request with all details
     */
    public function show($id)
    {
        try {
            $documentRequest = DocumentRequest::with('user:id,name,email')
                ->findOrFail($id);

            // Format attachments with proper URLs
            $attachments = [];
            if ($documentRequest->attachments) {
                $attachmentPaths = is_string($documentRequest->attachments) 
                    ? json_decode($documentRequest->attachments, true) 
                    : (array)$documentRequest->attachments;
                
                $attachments = array_map(function ($path) {
                    return [
                        'name' => basename($path),
                        'path' => $path,
                        'url' => Storage::url($path),
                        'type' => $this->getFileType($path),
                    ];
                }, $attachmentPaths ?? []);
            }

            $formattedRequest = [
                'id' => $documentRequest->id,
                'user' => [
                    'id' => $documentRequest->user?->id,
                    'name' => $documentRequest->user?->name ?? 'Unknown User',
                    'email' => $documentRequest->user?->email ?? 'No Email',
                ],
                'document_type' => $documentRequest->document_type ?? 'N/A',
                'purpose' => $documentRequest->purpose ?? 'No purpose specified',
                'status' => $documentRequest->status ?? 'pending',
                'admin_note' => $documentRequest->admin_note ?? '',
                'attachments' => $attachments,
                'signature' => [
                    'admin_signature' => $documentRequest->admin_signature ?? null,
                    'admin_signature_date' => $documentRequest->admin_signature_date ?? null,
                    'user_signature' => $documentRequest->user_signature ?? null,
                    'user_signature_date' => $documentRequest->user_signature_date ?? null,
                ],
                'workflow_history' => $documentRequest->workflow_history 
                    ? json_decode($documentRequest->workflow_history, true) 
                    : [],
                'created_at' => $documentRequest->created_at?->toISOString() ?? null,
                'updated_at' => $documentRequest->updated_at?->toISOString() ?? null,
            ];

            return Inertia::render('Admin/Documents/Show', [
                'request' => $formattedRequest,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Document not found: ' . $id);
            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Document not found']);

        } catch (\Exception $e) {
            Log::error('Admin Document Show Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.documents.index')
                ->withErrors(['error' => 'Failed to load document details.']);
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
                'status' => 'required|in:pending,processing,ready_for_pickup,completed,rejected',
                'admin_note' => 'nullable|string|max:1000',
                'admin_signature' => 'nullable|string',
            ]);

            // Get current workflow history
            $workflowHistory = $documentRequest->workflow_history 
                ? json_decode($documentRequest->workflow_history, true) 
                : [];

            // Add new workflow entry
            $workflowHistory[] = [
                'status' => $validated['status'],
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'timestamp' => now()->toIso8601String(),
                'note' => $validated['admin_note'] ?? null,
            ];

            // Prepare update data
            $updateData = [
                'status' => $validated['status'],
                'admin_note' => $validated['admin_note'] ?? null,
                'workflow_history' => json_encode($workflowHistory),
            ];

            // Add signature if provided and status is completed
            if (!empty($validated['admin_signature']) && $validated['status'] === 'completed') {
                $updateData['admin_signature'] = $validated['admin_signature'];
                $updateData['admin_signature_date'] = now();
            }

            $documentRequest->update($updateData);

            return redirect()
                ->back()
                ->with('success', "Document status updated to '{$validated['status']}'");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Document not found']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('Admin Document Update Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update document. Please try again.']);
        }
    }

    /**
     * Handle document approval with signature
     */
    public function approve(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'admin_signature' => 'required|string',
                'admin_note' => 'nullable|string|max:1000',
            ]);

            // Get current workflow history
            $workflowHistory = $documentRequest->workflow_history 
                ? json_decode($documentRequest->workflow_history, true) 
                : [];

            // Add approval to workflow
            $workflowHistory[] = [
                'status' => 'approved',
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'timestamp' => now()->toIso8601String(),
                'note' => $validated['admin_note'] ?? 'Document approved',
            ];

            $documentRequest->update([
                'status' => 'completed',
                'admin_signature' => $validated['admin_signature'],
                'admin_signature_date' => now(),
                'admin_note' => $validated['admin_note'] ?? null,
                'workflow_history' => json_encode($workflowHistory),
            ]);

            return redirect()
                ->back()
                ->with('success', 'Document approved successfully');

        } catch (\Exception $e) {
            Log::error('Document Approval Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to approve document']);
        }
    }

    /**
     * Handle document rejection
     */
    public function reject(Request $request, $id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            // Get current workflow history
            $workflowHistory = $documentRequest->workflow_history 
                ? json_decode($documentRequest->workflow_history, true) 
                : [];

            // Add rejection to workflow
            $workflowHistory[] = [
                'status' => 'rejected',
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'timestamp' => now()->toIso8601String(),
                'reason' => $validated['rejection_reason'],
            ];

            $documentRequest->update([
                'status' => 'rejected',
                'admin_note' => $validated['rejection_reason'],
                'workflow_history' => json_encode($workflowHistory),
            ]);

            return redirect()
                ->back()
                ->with('success', 'Document rejected successfully');

        } catch (\Exception $e) {
            Log::error('Document Rejection Error: ' . $e->getMessage(), [
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to reject document']);
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

            $filePath = $validated['path'];

            // Security check - ensure file belongs to this document
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
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to download file']);
        }
    }

    /**
     * Helper function to determine file type
     */
    private function getFileType($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}