<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstablishmentController extends Controller
{
public function index()
{
    return Inertia::render('Establishments/Index', [
        // List of all shops
        'establishments' => Establishment::all(),

        // List of YOUR personal bookings (newest first)
        'myBookings' => Booking::with('establishment')
                            ->where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get()
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string', // food, hotel, etc.
            'location' => 'required|string',
            'contact_number' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB image
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('establishments', 'public');
            $validated['image'] = $path;
        }

        Establishment::create($validated);

        return redirect()->back();
    }
}