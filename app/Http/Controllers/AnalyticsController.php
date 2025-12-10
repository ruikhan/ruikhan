<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Concern;
use App\Models\BillPayment;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Total Counts
        $stats = [
            'total_residents' => User::where('role', 'resident')->count(),
            'total_businesses' => User::where('role', 'business_owner')->count(),
            'pending_docs' => DocumentRequest::where('status', 'pending')->count(),
            'open_concerns' => Concern::where('status', 'open')->count(),
            'total_revenue' => BillPayment::where('status', 'successful')->sum('amount'),
        ];

        // 2. Chart Data: Concerns by Category (Pie Chart)
        $concernsByCategory = Concern::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // 3. Chart Data: Monthly Revenue (Line Chart)
        // (Simplified for demo: Grouping by Month)
        $monthlyRevenue = BillPayment::selectRaw('MONTH(created_at) as month, sum(amount) as total')
            ->where('status', 'successful')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Admin/Analytics', [
            'stats' => $stats,
            'chartData' => [
                'concerns' => $concernsByCategory,
                'revenue' => $monthlyRevenue
            ]
        ]);
    }
}