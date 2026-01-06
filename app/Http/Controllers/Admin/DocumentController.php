<?php
// ============================================================================
// FILE 1: app/Http/Controllers/AdminDocumentController.php
// REPLACE YOUR EXISTING update() METHOD WITH THIS
// ============================================================================

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of document requests
     */
    public function index()
    {
        $requests = DocumentRequest::with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Documents/Index', [
            'requests' => $requests
        ]);
    }

    /**
     * Display a specific document request
     */
    public function show($id)
    {
        $docRequest = DocumentRequest::with('user')->findOrFail($id);

        return Inertia::render('Admin/Documents/Requests/Show', [
            'docRequest' => $docRequest,
            'user' => $docRequest->user,
        ]);
    }

    /**
     * Update the document request
     * 🔔 THIS IS WHERE NOTIFICATIONS ARE SENT
     */
    public function update(Request $request, $id)
    {
        // Find the document request
        $docRequest = DocumentRequest::findOrFail($id);
        
        // Store old status BEFORE updating
        $oldStatus = $docRequest->status;

        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready_for_pickup,completed,rejected',
            'admin_remarks' => 'nullable|string|max:1000',
            'appointment_date' => 'nullable|date',
        ]);

        // Update the document request
        $docRequest->update($validated);

        // 🔔 CRITICAL: Send notification if status changed
        if ($oldStatus !== $docRequest->status) {
            try {
                // Send notification to the user who made the request
                $docRequest->user->notify(
                    new DocumentStatusUpdated($docRequest, $oldStatus)
                );

                // Log success
                \Log::info('Document status notification sent', [
                    'request_id' => $docRequest->id,
                    'user_id' => $docRequest->user_id,
                    'user_email' => $docRequest->user->email,
                    'old_status' => $oldStatus,
                    'new_status' => $docRequest->status,
                    'tracking_code' => $docRequest->tracking_code,
                ]);

            } catch (\Exception $e) {
                // Log error but don't fail the update
                \Log::error('Failed to send document status notification', [
                    'error' => $e->getMessage(),
                    'request_id' => $docRequest->id,
                    'user_id' => $docRequest->user_id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Return back with success message
        return redirect()
            ->back()
            ->with('success', 'Document request updated successfully. Notification sent to user.');
    }
}