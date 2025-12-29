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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ---------------------------------------------------
        // SCENARIO A: THE ADMINISTRATOR (Command Center)
        // ---------------------------------------------------
        if (trim(strtolower($user->role)) === 'admin') { 
            
            // 1. Fetch System-Wide Data
            $totalCitizens = User::where('role', '!=', 'admin')->count();
            
            // Mock Data (Replace with real DB queries later)
            $pendingPermits = 14; 
            $todaysRevenue = 25400.00; 
            $activeIncidents = 2; 

            // 2. Render the Admin Vue Page with ALL required props
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
                ],
                // ✅ FIX: Added missing 'pendingTasks' to prevent crash
                'pendingTasks' => [
                    ['id' => 101, 'task' => 'Review Mayor’s Permit Applications', 'priority' => 'High'],
                    ['id' => 102, 'task' => 'Approve Barangay Budget Allocation', 'priority' => 'Medium'],
                    ['id' => 103, 'task' => 'Update Disaster Response Protocols', 'priority' => 'Low'],
                ],
                // ✅ FIX: Added missing 'departmentLoad' to prevent crash
                'departmentLoad' => [
                    ['name' => 'Civil Registrar', 'count' => 45],
                    ['name' => 'Business Permits', 'count' => 32],
                    ['name' => 'Health Office', 'count' => 28],
                    ['name' => 'Engineering', 'count' => 15],
                ]
            ]);
        }

        // ---------------------------------------------------
        // SCENARIO B: THE CITIZEN (Personal Dashboard)
        // ---------------------------------------------------
        else {
            $myRequests = DocumentRequest::where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

            return Inertia::render('Dashboard', [
                'auth' => [
                    'user' => $user
                ],
                'requests' => $myRequests, 
                'notifications' => [] 
            ]);
        }
    }
}