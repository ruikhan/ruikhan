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
     public function apply(Request $request)
    {
        $validated = $request->validate([
            'application_type' => 'required|string',
            'application_data' => 'required|array',
            'attachments'      => 'nullable|array',
            'attachments.*'    => 'nullable|file|max:5120',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store("health-applications/{$validated['application_type']}", 'public');
            }
        }

        \App\Models\HealthApplication::create([
            'user_id'          => auth()->id(),
            'application_type' => $validated['application_type'],
            'application_data' => $validated['application_data'],
            'attachments'      => $attachmentPaths ?: null,
            'status'           => 'pending',
        ]);

        return redirect()->back()
            ->with('success', '✅ Health application submitted! We will notify you once reviewed.');
    }
}