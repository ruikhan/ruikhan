<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HealthController extends Controller
{
    public function index()
{
    // In a real app, fetch available slots from DB
    return Inertia::render('Health/Index');
}

public function store(Request $request)
{
    // Save appointment logic here
    return redirect()->back()->with('message', 'Appointment Confirmed');
}
}
