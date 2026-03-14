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

            // ── Social Aid (safe — model may not exist yet) ───
            $socialAidStats    = [];
            $socialAidByProgram = collect();
            $recentSocialAid   = collect();

            if (class_exists(\App\Models\SocialAidApplication::class)) {
                $m = \App\Models\SocialAidApplication::class;
                $socialAidStats = [
                    'total'        => $m::count(),
                    'pending'      => $m::where('status', 'pending')->count(),
                    'under_review' => $m::where('status', 'under_review')->count(),
                    'approved'     => $m::where('status', 'approved')->count(),
                    'released'     => $m::where('status', 'released')->count(),
                    'rejected'     => $m::where('status', 'rejected')->count(),
                ];
                $socialAidByProgram = $m::select('program_type', DB::raw('count(*) as count'))
                    ->groupBy('program_type')
                    ->get()
                    ->map(fn($r) => ['label' => $r->program_type, 'count' => $r->count]);
                $recentSocialAid = $m::with('user:id,name')
                    ->latest()->limit(5)->get()
                    ->map(fn($a) => [
                        'id'       => $a->id,
                        'tracking' => $a->tracking_code ?? '',
                        'name'     => $a->user?->name ?? 'Unknown',
                        'program'  => $a->program_type ?? '',
                        'status'   => $a->status,
                        'time'     => $a->created_at->diffForHumans(),
                    ]);
            }

            // ── Health Applications (safe) ────────────────────
            $healthStats  = [];
            $healthByType = collect();
            $recentHealth = collect();

            if (class_exists(\App\Models\HealthApplication::class)) {
                $m = \App\Models\HealthApplication::class;
                $healthStats = [
                    'total'        => $m::count(),
                    'pending'      => $m::where('status', 'pending')->count(),
                    'under_review' => $m::where('status', 'under_review')->count(),
                    'scheduled'    => $m::where('status', 'scheduled')->count(),
                    'completed'    => $m::where('status', 'completed')->count(),
                    'rejected'     => $m::where('status', 'rejected')->count(),
                ];
                $healthByType = $m::select('application_type', DB::raw('count(*) as count'))
                    ->groupBy('application_type')
                    ->get()
                    ->map(fn($r) => ['label' => $r->application_type, 'count' => $r->count]);
                $recentHealth = $m::with('user:id,name')
                    ->latest()->limit(5)->get()
                    ->map(fn($a) => [
                        'id'       => $a->id,
                        'tracking' => $a->tracking_code ?? '',
                        'name'     => $a->user?->name ?? 'Unknown',
                        'type'     => $a->application_type ?? '',
                        'status'   => $a->status,
                        'time'     => $a->created_at->diffForHumans(),
                    ]);
            }

            // ── Environment Reports (safe) ────────────────────
            $envStats      = [];
            $envBySeverity = collect();
            $recentEnv     = collect();

            if (class_exists(\App\Models\EnvironmentReport::class)) {
                $m = \App\Models\EnvironmentReport::class;
                $envStats = [
                    'total'         => $m::count(),
                    'pending'       => $m::where('status', 'pending')->count(),
                    'acknowledged'  => $m::where('status', 'acknowledged')->count(),
                    'investigating' => $m::where('status', 'investigating')->count(),
                    'resolved'      => $m::where('status', 'resolved')->count(),
                    'dismissed'     => $m::where('status', 'dismissed')->count(),
                    'critical'      => $m::where('severity', 'critical')
                                         ->whereNotIn('status', ['resolved', 'dismissed'])->count(),
                ];
                $envBySeverity = $m::select('severity', DB::raw('count(*) as count'))
                    ->groupBy('severity')
                    ->get()
                    ->map(fn($r) => ['label' => $r->severity, 'count' => $r->count]);
                $recentEnv = $m::with('user:id,name')
                    ->latest()->limit(5)->get()
                    ->map(fn($r) => [
                        'id'       => $r->id,
                        'tracking' => $r->tracking_code ?? '',
                        'name'     => $r->user?->name ?? 'Unknown',
                        'type'     => $r->report_type ?? '',
                        'severity' => $r->severity ?? '',
                        'status'   => $r->status,
                        'time'     => $r->created_at->diffForHumans(),
                    ]);
            }

            return Inertia::render('Admin/Dashboard', [
                'stats'          => $stats,
                'recentActivity' => $recentActivity,
                'pendingTasks'   => $pendingTasks,
                'departmentLoad' => $departmentLoad,
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
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Admin/Dashboard', [
                'stats'          => ['revenue' => 0, 'citizens' => 0],
                'recentActivity' => [],
                'pendingTasks'   => [],
                'departmentLoad' => [],
                'socialAid'      => ['stats' => [], 'byProgram' => [], 'recent' => []],
                'health'         => ['stats' => [], 'byType'    => [], 'recent' => []],
                'environment'    => ['stats' => [], 'bySeverity'=> [], 'recent' => []],
            ])->with('error', 'Failed to load dashboard data. Please refresh the page.');
        }
    }

    // ── Workflow Index / Show stubs ───────────────────────────────────────────
    // These render placeholder pages until full admin panels are built.

    public function socialAidIndex()
    {
        return Inertia::render('Admin/SocialAid/Index', ['applications' => []]);
    }

    public function socialAidShow($id)
    {
        return Inertia::render('Admin/SocialAid/Show', ['application' => null]);
    }

    public function healthIndex()
    {
        return Inertia::render('Admin/Health/Index', ['applications' => []]);
    }

    public function healthShow($id)
    {
        return Inertia::render('Admin/Health/Show', ['application' => null]);
    }

    public function environmentIndex()
    {
        return Inertia::render('Admin/Environment/Index', ['reports' => []]);
    }

    public function environmentShow($id)
    {
        return Inertia::render('Admin/Environment/Show', ['report' => null]);
    }

    // ─────────────────────────────────────────────────────────────────────────
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