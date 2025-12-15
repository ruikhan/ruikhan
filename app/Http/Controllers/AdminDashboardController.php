<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Fetch Real Data from Database
        $totalCitizens = User::count();
        
        // 2. Mock Data (Replace these with real Models later: e.g., Permit::where('status', 'pending')->count())
        $pendingPermits = 14; 
        $todaysRevenue = 25400.00; 
        $activeIncidents = 2; // e.g., Flooding in Brgy. San Jose

        // 3. Send to the View
        return Inertia::render('Dashboard', [
            'stats' => [
                'citizens' => $totalCitizens,
                'permits' => $pendingPermits,
                'revenue' => $todaysRevenue,
                'incidents' => $activeIncidents,
            ],
            'recentActivity' => [
                ['id' => 1, 'user' => 'Juan Dela Cruz', 'action' => 'Applied for Cedula', 'time' => '2 mins ago', 'status' => 'Pending'],
                ['id' => 2, 'user' => 'Maria Santos', 'action' => 'Reported Pothole', 'time' => '15 mins ago', 'status' => 'Review'],
                ['id' => 3, 'user' => 'Pedro Penduko', 'action' => 'Business Permit Renewal', 'time' => '1 hour ago', 'status' => 'Approved'],
            ]
        ]);
    }
}