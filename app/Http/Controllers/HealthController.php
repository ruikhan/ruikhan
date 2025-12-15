<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HealthController extends Controller
{
    public function index()
    {
        // Fetch doctors and user's history
        return Inertia::render('Health/Index', [
            'doctors' => Doctor::all(),
            'appointments' => Appointment::where('user_id', auth()->id())
                ->with('doctor')
                ->orderBy('scheduled_at', 'desc')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'type' => 'required|string',
            'symptoms' => 'nullable|string'
        ]);

        // Combine Date and Time
        $scheduledAt = \Carbon\Carbon::parse($validated['date'] . ' ' . $validated['time']);

        Appointment::create([
            'user_id' => auth()->id(),
            'doctor_id' => $validated['doctor_id'],
            'scheduled_at' => $scheduledAt,
            'type' => $validated['type'],
            'symptoms' => $validated['symptoms'],
            'status' => 'confirmed', // Auto-confirm for demo
        ]);

        return redirect()->back()->with('flash', ['success' => 'Appointment Booked Successfully!']);
    }
}