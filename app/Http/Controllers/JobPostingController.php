<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobPostingController extends Controller
{
    // 1. PUBLIC: Show all jobs (For Residents)
    public function index()
    {
        return Inertia::render('Jobs/Index', [
            'jobs' => JobPosting::where('is_active', true)->with('user')->latest()->get()
        ]);
    }

    // 2. BUSINESS ONLY: Show the "Create Job" form
    public function create()
    {
        return Inertia::render('Jobs/Create');
    }

    // 3. BUSINESS ONLY: Save the job
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'job_title' => 'required|string',
            'description' => 'required|string',
            'salary_range' => 'required|string',
            'location' => 'required|string',
        ]);

        $request->user()->jobPostings()->create($validated);

        return redirect()->route('jobs.index');
    }
}