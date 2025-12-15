<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'establishment_id' => 'required|exists:establishments,id',
            'service_type' => 'required|string',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Attach the logged-in user's ID automatically
        $validated['user_id'] = Auth::id();

        Booking::create($validated);

        return redirect()->back()->with('message', 'Booking successful!');
    }
}