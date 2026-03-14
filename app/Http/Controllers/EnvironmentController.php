<?php
namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
public function index()
{
    return Inertia::render('Environment/Index', [
        // 1. Current Sensor Readings
        'current' => [
            'aqi' => 42,
            'temp' => 31,
            'humidity' => 78,
            'water_level' => 'Normal',
        ],
        
        // 2. Mock History Data for Charts (Last 6 hours)
        'chart_data' => [
            'labels' => ['10:00', '11:00', '12:00', '1:00', '2:00', '3:00'],
            'aqi' => [35, 38, 42, 45, 42, 40],
            'temp' => [28, 29, 31, 32, 31, 30]
        ],

        // 3. GIS Map Data (Pili, Camarines Sur Context)
        'map_data' => [
            'evacuation_centers' => [
                ['id' => 1, 'name' => 'Pili Sports Complex', 'lat' => 13.585, 'lng' => 123.285, 'capacity' => '80% Full'],
                ['id' => 2, 'name' => 'Central School Hall', 'lat' => 13.578, 'lng' => 123.290, 'capacity' => 'Available'],
            ],
            'flood_zones' => [
                ['lat' => 13.580, 'lng' => 123.275, 'radius' => 500, 'risk' => 'High'], // Danger Zone
            ]
        ],

        // 4. Active Disaster Alert (Set to null for normal days, or populate object for disaster)
        'active_alert' => null 
        // To test disaster mode, uncomment below:
        /*
        'active_alert' => [
            'type' => 'Typhoon Warning',
            'level' => 'Signal No. 3',
            'message' => 'Super Typhoon detected. Heavy rainfall expected. Evacuate low-lying areas.',
            'timestamp' => now()->toDateTimeString()
        ]
        */
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type'  => 'required|string',
            'severity'     => 'required|in:low,moderate,high,critical',
            'location'     => 'required|string|max:255',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'report_data'  => 'required|array',
            'attachments'  => 'nullable|array',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('environment-reports', 'public');
            }
        }

        \App\Models\EnvironmentReport::create([
            'user_id'     => auth()->id(),
            'report_type' => $validated['report_type'],
            'severity'    => $validated['severity'],
            'location'    => $validated['location'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'report_data' => $validated['report_data'],
            'attachments' => $attachmentPaths ?: null,
            'status'      => 'pending',
        ]);

        return redirect()->back()
            ->with('success', '✅ Report submitted! Our environment team will review it shortly.');
    }
}