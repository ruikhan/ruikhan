<?php



namespace App\Http\Controllers;



use App\Models\DocumentRequest;

use App\Notifications\DocumentStatusUpdated; // We will create this next

use Illuminate\Http\Request;

use Inertia\Inertia;



class AdminDocumentController extends Controller

{

    public function index()

    {

        $requests = DocumentRequest::with('user')

            ->latest()

            ->get();



        return Inertia::render('Admin/Documents/Index', [

            'requests' => $requests

        ]);

    }



    public function show($id)

    {

        // Load user to display contact info

        $request = DocumentRequest::with('user')->findOrFail($id);



        return Inertia::render('Admin/Documents/Show', [

            'docRequest' => $request,

            'user' => $request->user

        ]);

    }



    // ✅ THE CRITICAL UPDATE

    public function update(Request $request, $id)

    {

        $docRequest = DocumentRequest::with('user')->findOrFail($id);



        $validated = $request->validate([

            'status' => 'required|in:pending,processing,ready_for_pickup,completed,rejected',

            // Changed 'admin_note' to 'admin_remarks' to match your DB column

            'admin_remarks' => 'nullable|string',

            // Added appointment date because it's in your Model

            'appointment_date' => 'nullable|date',

        ]);



        // 1. Update the record

        $docRequest->update([

            'status' => $validated['status'],

            'admin_remarks' => $validated['admin_remarks'],

            'appointment_date' => $validated['appointment_date'] ?? null,

        ]);



        // 2. Send Notification (Email + Database)

        // Only notify if status changed to avoid spamming on minor edits

        if ($docRequest->wasChanged('status')) {

            $docRequest->user->notify(new DocumentStatusUpdated($docRequest));

        }



        return redirect()->back()->with('success', 'Request status updated.');

    }

}