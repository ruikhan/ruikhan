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
            // ── Pending Tasks ─────────────────────────────────
            $pendingTasks = DocumentRequest::with('user:id,name,email')
                ->whereIn('status', ['pending', 'processing'])
                ->latest()
                ->limit(20)
                ->get()
                ->map(fn($r) => [
                    'id'     => $r->id,
                    'user'   => $r->user?->name ?? 'Unknown',
                    'detail' => $r->document_type ?? 'Document Request',
                    'type'   => $r->document_type ?? 'N/A',
                    'status' => $r->status === 'pending' ? 'urgent' : 'normal',
                    'time'   => $r->created_at->diffForHumans(),
                ]);

            // ── Recent Activity ───────────────────────────────
            $recentActivity = DocumentRequest::with('user:id,name,email')
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn($r) => [
                    'id'     => $r->id,
                    'user'   => $r->user?->name ?? 'Unknown',
                    'action' => $this->getActionText($r->status),
                    'time'   => $r->updated_at->diffForHumans(),
                ]);

            // ── Dashboard Stats ───────────────────────────────
            $stats = [
                'revenue'         => DocumentRequest::whereDate('created_at', today())
                                       ->where('status', 'completed')->count() * 50,
                'citizens'        => User::whereDate('created_at', today())->count(),
                'total_documents' => DocumentRequest::count(),
                'pending'         => DocumentRequest::where('status', 'pending')->count(),
                'processed_today' => DocumentRequest::whereDate('updated_at', today())
                                       ->whereIn('status', ['completed', 'ready_for_pickup'])->count(),
                'completion_rate' => $this->getCompletionRate(),
            ];

            // ── Department Load ───────────────────────────────
            // ✅ FIXED: Was using slugs ('civil_registry', 'treasury', 'engineering')
            //    which matched NOTHING in the DB. Now uses exact stored values.
            $departmentLoad = [
                [
                    'name'     => 'Civil Registrar',
                    'count'    => DocumentRequest::where('department', 'Municipal Civil Registrar')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 50,
                    'color'    => 'bg-gradient-to-r from-blue-500 to-blue-600',
                ],
                [
                    'name'     => "Treasurer's Office",
                    'count'    => DocumentRequest::where('department', "Treasurer's Office")
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 30,
                    'color'    => 'bg-gradient-to-r from-emerald-500 to-emerald-600',
                ],
                [
                    'name'     => 'Engineering Office',
                    'count'    => DocumentRequest::where('department', 'Engineering Office')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 40,
                    'color'    => 'bg-gradient-to-r from-purple-500 to-purple-600',
                ],
                [
                    'name'     => 'Health Office',
                    'count'    => DocumentRequest::where('department', 'Municipal Health Office')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 35,
                    'color'    => 'bg-gradient-to-r from-rose-500 to-rose-600',
                ],
                [
                    'name'     => 'Social Welfare',
                    'count'    => DocumentRequest::where('department', 'Social Welfare (MSWDO)')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 25,
                    'color'    => 'bg-gradient-to-r from-amber-500 to-amber-600',
                ],
                [
                    'name'     => 'Barangay',
                    'count'    => DocumentRequest::where('department', 'Barangay Certifications')
                                    ->whereIn('status', ['pending', 'processing'])->count(),
                    'capacity' => 60,
                    'color'    => 'bg-gradient-to-r from-cyan-500 to-cyan-600',
                ],
            ];

            // ── Document status breakdown (for charts) ────────
            $documentStats = DocumentRequest::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // ── Top departments by volume (for charts) ────────
            $departmentStats = DocumentRequest::select('department', DB::raw('count(*) as count'))
                ->groupBy('department')
                ->orderByDesc('count')
                ->limit(8)
                ->get()
                ->map(fn($r) => ['label' => $r->department, 'count' => $r->count]);

            return Inertia::render('Admin/Dashboard', [
                'stats'           => $stats,
                'recentActivity'  => $recentActivity,
                'pendingTasks'    => $pendingTasks,
                'departmentLoad'  => $departmentLoad,
                'documentStats'   => $documentStats,
                'departmentStats' => $departmentStats,
                'socialAid'       => $this->getSocialAidData(),
                'health'          => $this->getHealthData(),
                'environment'     => $this->getEnvironmentData(),
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Admin/Dashboard', [
                'stats'           => ['revenue' => 0, 'citizens' => 0, 'total_documents' => 0, 'pending' => 0, 'processed_today' => 0, 'completion_rate' => 0],
                'recentActivity'  => [],
                'pendingTasks'    => [],
                'departmentLoad'  => [],
                'documentStats'   => [],
                'departmentStats' => [],
                'socialAid'       => ['stats' => [], 'byProgram' => [], 'recent' => []],
                'health'          => ['stats' => [], 'byType'    => [], 'recent' => []],
                'environment'     => ['stats' => [], 'bySeverity'=> [], 'recent' => []],
            ])->with('error', 'Failed to load dashboard data. Please refresh.');
        }
    }

    // ── Workflow stub pages ───────────────────────────────────────────────────

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

    // ── Private helpers ───────────────────────────────────────────────────────

    private function getCompletionRate(): int
    {
        $total     = DocumentRequest::count();
        $completed = DocumentRequest::where('status', 'completed')->count();
        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }

    private function getSocialAidData(): array
    {
        if (!class_exists(\App\Models\SocialAidApplication::class)) {
            return ['stats' => [], 'byProgram' => [], 'recent' => []];
        }
        $m = \App\Models\SocialAidApplication::class;
        return [
            'stats' => [
                'total'        => $m::count(),
                'pending'      => $m::where('status', 'pending')->count(),
                'under_review' => $m::where('status', 'under_review')->count(),
                'approved'     => $m::where('status', 'approved')->count(),
                'released'     => $m::where('status', 'released')->count(),
                'rejected'     => $m::where('status', 'rejected')->count(),
            ],
            'byProgram' => $m::select('program_type', DB::raw('count(*) as count'))
                ->groupBy('program_type')->get()
                ->map(fn($r) => ['label' => $r->program_type, 'count' => $r->count]),
            'recent' => $m::with('user:id,name')->latest()->limit(5)->get()
                ->map(fn($a) => [
                    'id'       => $a->id,
                    'tracking' => $a->tracking_code ?? '',
                    'name'     => $a->user?->name ?? 'Unknown',
                    'program'  => $a->program_type ?? '',
                    'status'   => $a->status,
                    'time'     => $a->created_at->diffForHumans(),
                ]),
        ];
    }

    private function getHealthData(): array
    {
        if (!class_exists(\App\Models\HealthApplication::class)) {
            return ['stats' => [], 'byType' => [], 'recent' => []];
        }
        $m = \App\Models\HealthApplication::class;
        return [
            'stats' => [
                'total'        => $m::count(),
                'pending'      => $m::where('status', 'pending')->count(),
                'under_review' => $m::where('status', 'under_review')->count(),
                'scheduled'    => $m::where('status', 'scheduled')->count(),
                'completed'    => $m::where('status', 'completed')->count(),
                'rejected'     => $m::where('status', 'rejected')->count(),
            ],
            'byType' => $m::select('application_type', DB::raw('count(*) as count'))
                ->groupBy('application_type')->get()
                ->map(fn($r) => ['label' => $r->application_type, 'count' => $r->count]),
            'recent' => $m::with('user:id,name')->latest()->limit(5)->get()
                ->map(fn($a) => [
                    'id'       => $a->id,
                    'tracking' => $a->tracking_code ?? '',
                    'name'     => $a->user?->name ?? 'Unknown',
                    'type'     => $a->application_type ?? '',
                    'status'   => $a->status,
                    'time'     => $a->created_at->diffForHumans(),
                ]),
        ];
    }

    private function getEnvironmentData(): array
    {
        if (!class_exists(\App\Models\EnvironmentReport::class)) {
            return ['stats' => [], 'bySeverity' => [], 'recent' => []];
        }
        $m = \App\Models\EnvironmentReport::class;
        return [
            'stats' => [
                'total'         => $m::count(),
                'pending'       => $m::where('status', 'pending')->count(),
                'acknowledged'  => $m::where('status', 'acknowledged')->count(),
                'investigating' => $m::where('status', 'investigating')->count(),
                'resolved'      => $m::where('status', 'resolved')->count(),
                'dismissed'     => $m::where('status', 'dismissed')->count(),
                'critical'      => $m::where('severity', 'critical')
                                     ->whereNotIn('status', ['resolved', 'dismissed'])->count(),
            ],
            'bySeverity' => $m::select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')->get()
                ->map(fn($r) => ['label' => $r->severity, 'count' => $r->count]),
            'recent' => $m::with('user:id,name')->latest()->limit(5)->get()
                ->map(fn($r) => [
                    'id'       => $r->id,
                    'tracking' => $r->tracking_code ?? '',
                    'name'     => $r->user?->name ?? 'Unknown',
                    'type'     => $r->report_type ?? '',
                    'severity' => $r->severity ?? '',
                    'status'   => $r->status,
                    'time'     => $r->created_at->diffForHumans(),
                ]),
        ];
    }

    private function getActionText(string $status): string
    {
        return [
            'pending'          => 'submitted a document request',
            'processing'       => 'request is being processed',
            'ready_for_pickup' => 'request is ready for pickup',
            'completed'        => 'completed their request',
            'rejected'         => 'request was rejected',
        ][$status] ?? 'updated their request';
    }
}