<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ---------------------------------------------------
        // SCENARIO A: THE ADMINISTRATOR (Command Center)
        // ---------------------------------------------------
        if ($user->role === 'admin') { 
            
            // 1. Fetch System-Wide Data
            $totalCitizens = User::where('role', '!=', 'admin')->count();
            $pendingPermits = 14; // Mock data for now
            $todaysRevenue = 25400.00; 
            $activeIncidents = 2; 

            return Inertia::render('Admin/Dashboard', [
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

        // ---------------------------------------------------
        // SCENARIO B: THE CITIZEN (Personal Dashboard - Enhanced)
        // ---------------------------------------------------
        else {
            // Render the enhanced Dashboard.vue (Command Center for Citizens)
            return Inertia::render('Dashboard');
        }
    }
}