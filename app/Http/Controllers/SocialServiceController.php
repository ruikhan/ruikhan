<?php
namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SocialServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Social/Index', [
            'programs' => [
                [
                    'id' => 1, 
                    'title' => 'Academic Scholarship 2025', 
                    'type' => 'Education', 
                    'amount' => '₱10,000 / Sem',
                    'status' => 'Open'
                ],
                [
                    'id' => 2, 
                    'title' => 'Senior Citizen Pension', 
                    'type' => 'Financial Aid', 
                    'amount' => '₱1,500 / Month',
                    'status' => 'Always Open'
                ],
                [
                    'id' => 3, 
                    'title' => 'Emergency Disaster Fund', 
                    'type' => 'Calamity', 
                    'amount' => 'Varies',
                    'status' => 'Closed' // Only opens during typhoons
                ],
            ]
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->back()->with('message', 'Application Submitted');
    }
}