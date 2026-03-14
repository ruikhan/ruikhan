<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\User;
use App\Models\SocialAidApplication;
use App\Models\HealthApplication;
use App\Models\EnvironmentReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        try {
            // ── Document Requests ─────────────────────────────
            $pendingTasks = DocumentRequest::with('user:id,name,email')
                ->whereIn('status', ['pending', 'processing'])
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($request) {
                    return [
                        'id'     => $request->id,
                        'user'   => $request->user?->name ?? 'Unknown',
                        'detail' => $request->document_type ?? 'Document Request',
                        'type'   => $request->document_type ?? 'N/A',
                        'status' => $request->status === 'pending' ? 'urgent' : 'normal',
                        'time'   => $request->created_at->diffForHumans(),
                    ];
                });

            $recentActivity = DocumentRequest::with('user:id,name,email')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($request) {
                    return [
                        'id'     => $request->id,
                        'user'   => $request->user?->name ?? 'Unknown',
                        'action' => $this->getActionText($request->status),
                        'time'   => $request->updated_at->diffForHumans(),
                    ];
                });

            $stats = [
                'revenue'  => DocumentRequest::whereDate('created_at', today())->count() * 50,
                'citizens' => User::whereDate('created_at', today())->count(),
            ];

            $departmentLoad = [
                [
                    'name'     => 'Civil Registry',
                    'count'    => DocumentRequest::where('department', 'civil_registry')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 50,
                    'color'    => 'bg-gradient-to-r from-blue-500 to-blue-600',
                ],
                [
                    'name'     => 'Treasury',
                    'count'    => DocumentRequest::where('department', 'treasury')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 30,
                    'color'    => 'bg-gradient-to-r from-emerald-500 to-emerald-600',
                ],
                [
                    'name'     => 'Engineering',
                    'count'    => DocumentRequest::where('department', 'engineering')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 40,
                    'color'    => 'bg-gradient-to-r from-purple-500 to-purple-600',
                ],
            ];

            // ── Social Aid ────────────────────────────────────
            $socialAidStats = [
                'total'       => SocialAidApplication::count(),
                'pending'     => SocialAidApplication::where('status', 'pending')->count(),
                'under_review'=> SocialAidApplication::where('status', 'under_review')->count(),
                'approved'    => SocialAidApplication::where('status', 'approved')->count(),
                'released'    => SocialAidApplication::where('status', 'released')->count(),
                'rejected'    => SocialAidApplication::where('status', 'rejected')->count(),
            ];

            $socialAidByProgram = SocialAidApplication::select('program_type', DB::raw('count(*) as count'))
                ->groupBy('program_type')
                ->get()
                ->map(fn($r) => ['label' => $r->program_type, 'count' => $r->count]);

            $recentSocialAid = SocialAidApplication::with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'id'           => $a->id,
                    'tracking'     => $a->tracking_code,
                    'name'         => $a->user?->name ?? 'Unknown',
                    'program'      => $a->program_type,
                    'status'       => $a->status,
                    'time'         => $a->created_at->diffForHumans(),
                ]);

            // ── Health Applications ───────────────────────────
            $healthStats = [
                'total'       => HealthApplication::count(),
                'pending'     => HealthApplication::where('status', 'pending')->count(),
                'under_review'=> HealthApplication::where('status', 'under_review')->count(),
                'scheduled'   => HealthApplication::where('status', 'scheduled')->count(),
                'completed'   => HealthApplication::where('status', 'completed')->count(),
                'rejected'    => HealthApplication::where('status', 'rejected')->count(),
            ];

            $healthByType = HealthApplication::select('application_type', DB::raw('count(*) as count'))
                ->groupBy('application_type')
                ->get()
                ->map(fn($r) => ['label' => $r->application_type, 'count' => $r->count]);

            $recentHealth = HealthApplication::with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'id'       => $a->id,
                    'tracking' => $a->tracking_code,
                    'name'     => $a->user?->name ?? 'Unknown',
                    'type'     => $a->application_type,
                    'status'   => $a->status,
                    'time'     => $a->created_at->diffForHumans(),
                ]);

            // ── Environment Reports ───────────────────────────
            $envStats = [
                'total'        => EnvironmentReport::count(),
                'pending'      => EnvironmentReport::where('status', 'pending')->count(),
                'acknowledged' => EnvironmentReport::where('status', 'acknowledged')->count(),
                'investigating' => EnvironmentReport::where('status', 'investigating')->count(),
                'resolved'     => EnvironmentReport::where('status', 'resolved')->count(),
                'dismissed'    => EnvironmentReport::where('status', 'dismissed')->count(),
                'critical'     => EnvironmentReport::where('severity', 'critical')->whereNotIn('status', ['resolved', 'dismissed'])->count(),
            ];

            $envBySeverity = EnvironmentReport::select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')
                ->get()
                ->map(fn($r) => ['label' => $r->severity, 'count' => $r->count]);

            $recentEnv = EnvironmentReport::with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($r) => [
                    'id'       => $r->id,
                    'tracking' => $r->tracking_code,
                    'name'     => $r->user?->name ?? 'Unknown',
                    'type'     => $r->report_type,
                    'severity' => $r->severity,
                    'status'   => $r->status,
                    'time'     => $r->created_at->diffForHumans(),
                ]);

            return Inertia::render('Admin/Dashboard', [
                'stats'          => $stats,
                'recentActivity' => $recentActivity,
                'pendingTasks'   => $pendingTasks,
                'departmentLoad' => $departmentLoad,

                // Workflow sections
                'socialAid' => [
                    'stats'     => $socialAidStats,
                    'byProgram' => $socialAidByProgram,
                    'recent'    => $recentSocialAid,
                ],
                'health' => [
                    'stats'  => $healthStats,
                    'byType' => $healthByType,
                    'recent' => $recentHealth,
                ],
                'environment' => [
                    'stats'      => $envStats,
                    'bySeverity' => $envBySeverity,
                    'recent'     => $recentEnv,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Admin/Dashboard', [
                'stats'          => ['revenue' => 0, 'citizens' => 0],
                'recentActivity' => [],
                'pendingTasks'   => [],
                'departmentLoad' => [],
                'socialAid'      => ['stats' => [], 'byProgram' => [], 'recent' => []],
                'health'         => ['stats' => [], 'byType'   => [], 'recent' => []],
                'environment'    => ['stats' => [], 'bySeverity' => [], 'recent' => []],
            ])->with('error', 'Failed to load dashboard data. Please refresh the page.');
        }
    }

    private function getActionText($status)
    {
        return [
            'pending'          => 'submitted a document request',
            'processing'       => 'request is being processed',
            'ready_for_pickup' => 'request is ready for pickup',
            'completed'        => 'completed their request',
        ][$status] ?? 'updated their request';
    }
}