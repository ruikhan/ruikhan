<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EmergencyController extends Controller
{
    public function index()
    {
        return Inertia::render('Emergency/Index', [
            'hotlines' => [
                ['name' => 'PNP Police', 'number' => '911', 'icon' => '🚓', 'color' => 'blue'],
                ['name' => 'Fire Dept', 'number' => '160', 'icon' => '🚒', 'color' => 'orange'],
                ['name' => 'Ambulance', 'number' => '8888', 'icon' => '🚑', 'color' => 'red'],
                ['name' => 'Disaster Office', 'number' => '0912-345-6789', 'icon' => '🌪️', 'color' => 'slate'],
            ]
        ]);
    }

    public function store(Request $request)
    {
        // In a real app, this triggers SMS/GPS alerts to authorities
        return redirect()->back()->with('message', 'SOS Signal Sent! Rescue is on the way.');
    }
}