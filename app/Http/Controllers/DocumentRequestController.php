<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str; // Needed for random tracking code
use App\Events\NewDocumentRequest;

class DocumentRequestController extends Controller
{
    // 1. Show the list of my requests
    public function index()
    {
        $myRequests = DocumentRequest::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return Inertia::render('Documents/Index', [
            'requests' => $myRequests
        ]);
    }

    // 2. Show the "Create Request" Form (NEW)
    public function create()
    {
        return Inertia::render('Documents/Create'); 
    }

    // 3. Handle the Form Submission (UPDATED)
    public function store(Request $request)
    {
        // A. Smart Validation
        $rules = [
            'type' => 'required|string', // Maps to 'document_type' in DB
            'category' => 'required|in:personal,business',
            'purpose' => 'required|string|max:500',
            'civil_status' => 'required|string',
            'years_of_residency' => 'required|integer',
            'contact_number' => 'required|string',
            'valid_id' => 'required|image|max:2048', // Max 2MB image
        ];

        // Conditional: If Business, these become required
        if ($request->category === 'business') {
            $rules['business_name'] = 'required|string';
            $rules['tin_number'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // B. Handle File Upload
        $idPath = null;
        if ($request->hasFile('valid_id')) {
            // Stores inside "storage/app/public/ids"
            $idPath = $request->file('valid_id')->store('ids', 'public');
        }

        // C. Create the Record
        // We use $request->user()->... to automatically attach the user_id
        $newRequest = $request->user()->documentRequests()->create([
            'tracking_code' => 'DOC-' . strtoupper(Str::random(8)), // Auto-generate code
            'document_type' => $validated['type'], // Mapping Vue 'type' to DB 'document_type'
            'category' => $validated['category'],
            'purpose' => $validated['purpose'],
            'civil_status' => $validated['civil_status'],
            'years_of_residency' => $validated['years_of_residency'],
            'contact_number' => $validated['contact_number'],
            'business_name' => $request->business_name ?? null,
            'tin_number' => $request->tin_number ?? null,
            'valid_id_path' => $idPath,
            'status' => 'Pending'
        ]);

        // D. FIRE THE EVENT (Keep your real-time notification)
        event(new NewDocumentRequest($newRequest));

        // E. Redirect back to dashboard or index
        return redirect()->route('dashboard')->with('success', 'Request submitted successfully!');
    }
}