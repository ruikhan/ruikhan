<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDocumentController extends Controller
{
    // 1. Show the Master List
    public function index()
    {
        // Get all requests, newest first, including the user's name
        $requests = DocumentRequest::with('user')
            ->latest()
            ->get();

        return Inertia::render('Admin/Documents/Index', [
            'requests' => $requests
        ]);
    }

    // 2. Process a Request (Approve/Reject)
    public function update(Request $request, $id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready_for_pickup,completed,rejected',
            'admin_note' => 'nullable|string'
        ]);

        $docRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note']
        ]);

        return redirect()->back();
    }
    // 3. Show Specific Request Details (The Dossier)
    public function show($id)
    {
        $request = DocumentRequest::with('user')->findOrFail($id);

        return Inertia::render('Admin/Documents/Show', [
            'docRequest' => $request
        ]);
    }
}