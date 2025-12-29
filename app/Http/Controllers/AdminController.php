<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        // --- 1. Calculate Real Stats ---
        // Assuming 150 PHP revenue per completed doc.
        $completedCount = DocumentRequest::where('status', 'completed')->count();
        $stats = [
            'revenue' => $completedCount * 150, 
            'citizens' => User::where('role', '!=', 'admin')->count(),
        ];

        // --- 2. Fetch Pending Tasks (The "Priority Queue") ---
        $pendingTasks = DocumentRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'type' => Str::title(str_replace('_', ' ', $doc->document_type)),
                    // ✅ FIX: Check if user exists before accessing name
                    'user' => $doc->user ? $doc->user->name : 'Unknown User', 
                    'detail' => Str::limit($doc->purpose, 30),
                    'status' => 'normal',
                    'time' => $doc->created_at->diffForHumans(),
                ];
            });

        // --- 3. Calculate Department Load ---
        $activeDocs = DocumentRequest::where('status', '!=', 'completed')->get();
        
        $departmentLoad = [
            [
                'name' => 'Civil Registry',
                'count' => $activeDocs->whereIn('document_type', ['birth_certificate', 'marriage_certificate', 'death_certificate'])->count(),
                'capacity' => 80,
                'color' => 'bg-blue-500'
            ],
            [
                'name' => 'Health Office',
                'count' => $activeDocs->where('document_type', 'health_certificate')->count(),
                'capacity' => 100,
                'color' => 'bg-red-500'
            ],
            [
                'name' => 'Social Welfare',
                'count' => $activeDocs->where('document_type', 'indigency')->count(),
                'capacity' => 70,
                'color' => 'bg-yellow-500'
            ],
            [
                'name' => 'Engineering',
                'count' => $activeDocs->whereIn('document_type', ['building_permit', 'business_permit'])->count(),
                'capacity' => 50,
                'color' => 'bg-green-500'
            ],
        ];

        // --- 4. Recent Activity Log ---
        $recentActivity = DocumentRequest::with('user')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    // ✅ FIX: Check if user exists here too
                    'user' => $doc->user ? $doc->user->name : 'Deleted User',
                    'action' => 'status updated to ' . strtoupper($doc->status),
                    'time' => $doc->updated_at->format('h:i A'),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'pendingTasks' => $pendingTasks,
            'departmentLoad' => $departmentLoad,
            'recentActivity' => $recentActivity
        ]);
    }
}