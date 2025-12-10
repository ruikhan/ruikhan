<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia; // We use Inertia because you chose Vue.js
use App\Events\NewDocumentRequest;

class DocumentRequestController extends Controller
{
    // 1. Show the "Request Document" Form
    public function index()
    {
        // Get all requests made by the specific logged-in user
        $myRequests = DocumentRequest::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Render the Vue page (we will create this file next)
        return Inertia::render('Documents/Index', [
            'requests' => $myRequests
        ]);
    }

    // 2. Handle the Form Submission
public function store(Request $request)
{
    $validated = $request->validate([
        'document_type' => 'required|string',
        'purpose' => 'required|string|max:500',
    ]);

    // 1. Create the request and store it in a variable
    $newRequest = $request->user()->documentRequests()->create([
        'document_type' => $validated['document_type'],
        'purpose' => $validated['purpose'],
        'status' => 'pending'
    ]);

    // 2. FIRE THE EVENT! (This sends the data to WebSockets)
    event(new NewDocumentRequest($newRequest));

    return redirect()->back();
}
}