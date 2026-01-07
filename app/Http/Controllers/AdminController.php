<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        try {
            // Get pending tasks
            $pendingTasks = DocumentRequest::with('user:id,name,email')
                ->whereIn('status', ['pending', 'processing'])
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => $request->user?->name ?? 'Unknown',
                        'detail' => $request->document_type ?? 'Document Request',
                        'type' => $request->document_type ?? 'N/A',
                        'status' => $request->status === 'pending' ? 'urgent' : 'normal',
                        'time' => $request->created_at->diffForHumans(),
                    ];
                });

            // Get recent activity
            $recentActivity = DocumentRequest::with('user:id,name,email')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'user' => $request->user?->name ?? 'Unknown',
                        'action' => $this->getActionText($request->status),
                        'time' => $request->updated_at->diffForHumans(),
                    ];
                });

            // Get stats
            $stats = [
                'revenue' => DocumentRequest::whereDate('created_at', today())
                    ->count() * 50, // Assuming ₱50 per document
                'citizens' => User::whereDate('created_at', today())->count(),
            ];

            // Get department load
            $departmentLoad = [
                [
                    'name' => 'Civil Registry',
                    'count' => DocumentRequest::where('department', 'civil_registry')
                        ->whereIn('status', ['pending', 'processing'])
                        ->count(),
                    'capacity' => 50,
                    'color' => 'bg-gradient-to-r from-blue-500 to-blue-600',
                ],
                [
                    'name' => 'Treasury',
                    'count' => DocumentRequest::where('department', 'treasury')
                        ->whereIn('status', ['pending', 'processing'])
                        ->count(),
                    'capacity' => 30,
                    'color' => 'bg-gradient-to-r from-emerald-500 to-emerald-600',
                ],
                [
                    'name' => 'Engineering',
                    'count' => DocumentRequest::where('department', 'engineering')
                        ->whereIn('status', ['pending', 'processing'])
                        ->count(),
                    'capacity' => 40,
                    'color' => 'bg-gradient-to-r from-purple-500 to-purple-600',
                ],
            ];

            return Inertia::render('Admin/Dashboard', [
                'stats' => $stats,
                'recentActivity' => $recentActivity,
                'pendingTasks' => $pendingTasks,
                'departmentLoad' => $departmentLoad,
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Admin Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe defaults
            return Inertia::render('Admin/Dashboard', [
                'stats' => ['revenue' => 0, 'citizens' => 0],
                'recentActivity' => [],
                'pendingTasks' => [],
                'departmentLoad' => [],
            ])->with('error', 'Failed to load dashboard data. Please refresh the page.');
        }
    }

    private function getActionText($status)
    {
        $actions = [
            'pending' => 'submitted a document request',
            'processing' => 'request is being processed',
            'ready_for_pickup' => 'request is ready for pickup',
            'completed' => 'completed their request',
        ];

        return $actions[$status] ?? 'updated their request';
    }
}