<?php

namespace App\Http\Controllers;

use App\Models\Concern;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Events\NewConcernFiled;

class ConcernController extends Controller
{
    // 1. Show the Form
    public function create()
    {
        return Inertia::render('Concerns/Create');
    }

 // 2. Handle the Submission (with Image)
    public function store(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'category' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $imagePath = null;

        // CHECK: Did the user upload a file?
        if ($request->hasFile('evidence')) {
            $imagePath = $request->file('evidence')->store('concerns', 'public');
        }

        // --- MODIFIED SECTION STARTS HERE ---

        // 1. Assign the result to a variable named $concern
        $concern = $request->user()->concerns()->create([
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'evidence_image_path' => $imagePath,
            'status' => 'open'
        ]);

        // 2. FIRE THE EVENT using that variable
        event(new NewConcernFiled($concern));

        // --- MODIFIED SECTION ENDS HERE ---

        return redirect()->route('dashboard');
    }
    
}